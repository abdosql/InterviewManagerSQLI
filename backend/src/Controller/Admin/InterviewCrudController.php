<?php

namespace App\Controller\Admin;

use App\Candidate\Query\FindCandidate;
use App\Entity\Interview;
use App\Form\InterviewType;
use App\Interview\Command\CreateInterviewCommand;
use App\Interview\Command\DeleteInterviewCommand;
use App\Interview\Command\Handler\CommandHandlerInterface;
use App\Publisher\MercurePublisher;
use App\Publisher\PublisherInterface;
use App\Services\Impl\InterviewService;
use App\User\Query\FindUser;
use App\User\Query\GetUsersByIds;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use phpDocumentor\Reflection\Types\This;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class InterviewCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CommandHandlerInterface $commandHandler,
        private readonly MessageBusInterface $messageBus,
        private readonly InterviewService $interviewService,
        private readonly FindCandidate $findCandidate,
        private readonly FindUser $findUser,
        private readonly GetUsersByIds $getUsersByIds,
        private readonly PublisherInterface  $mercurePublisher,
        private readonly AdminUrlGenerator       $adminUrlGenerator,

    )
    {
    }

//    public function configureCrud(Crud $crud): Crud
//    {
//        return $crud
//            ->setEntityLabelInSingular('Custom Entity')
//            ->setEntityLabelInPlural('Custom Entities')
//            ->setPageTitle(Crud::PAGE_DETAIL, 'Details of %entity_label_singular%')
//            ->overrideTemplates(
//                [
//                    'crud/detail' => 'interview/show.html.twig',
//                ]
//            );
//    }


    public static function getEntityFqcn(): string
    {
        return Interview::class;
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::DELETE, Action::EDIT, Action::NEW);
    }



    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnForms();
        yield TextField::new('interview_location')->hideOnForm();
        yield DateField::new('interview_date')->hideOnForm();

        yield AssociationField::new('candidate')
            ->formatValue(function ($value) {
                return $value->getFirstName() . ' ' . $value->getLastName();
            });

        yield AssociationField::new('evaluators', 'Evaluators')
            ->formatValue(function ($value) {
                $evaluators = [];
                foreach ($value as $evaluator) {
                    $evaluators[] = $evaluator->getFirstName() . ' ' . $evaluator->getLastName();
                }
                return implode(', ', $evaluators);
            });


    }

    #[Route('/interview/calendar', name: 'interview_calendar', methods: ["GET"])]

    public function viewCalendar(): Response
    {
        $interview = new Interview();
        $form = $this->createForm(InterviewType::class, $interview);

        return $this->render('interview/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    //The Get Method Is temporarily don't panic a chef (:

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    #[Route('/api/interviews', name: 'api_create_interview', methods: ["post", "get"])]
    public function createInterview(Request $request): JsonResponse
    {
        if ($request->isMethod('POST')){
            $data = json_decode($request->getContent(), true);

            if (!$this->isCsrfTokenValid('interview', $data['token'] ?? '')) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Invalid CSRF token'],
                    Response::HTTP_BAD_REQUEST);
            }

            if (empty($data['date']) || empty($data['location']) || empty($data['candidate']) || empty($data['evaluators'])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Some fields are missing'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            try {
                $interview = new Interview();
                $candidate = $this->findCandidate->findItem($data['candidate']);
                if (!$candidate) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Invalid candidate',
                    ], Response::HTTP_BAD_REQUEST);
                }
                $evaluators = $this->getUsersByIds->findItems(["ids" => $data['evaluators']]);
                if (!$evaluators) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Invalid candidate or evaluator',
                    ], Response::HTTP_BAD_REQUEST);
                }

                foreach ($evaluators as $evaluator) {
                    $interview->addEvaluator($evaluator);
                }




                $interviewDate = \DateTime::createFromFormat('Y-m-d\TH:i', $data['date']);
                if (!$interviewDate) {
                    return new JsonResponse(['success' => false, 'message' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
                }

                $interview
                    ->setInterviewDate($interviewDate)
                    ->setInterviewLocation($data['location'])
                    ->setCandidate($candidate)
                    ->setHrManager($this->getUser());
                ;
                $command = new CreateInterviewCommand($interview, $this->interviewService, $this->messageBus);
                $this->commandHandler->handle($command);
                //hna notifications
                $url = $this->adminUrlGenerator
                    ->setController(InterviewCrudController::class)
                    ->setAction('detail')
                    ->setEntityId($interview->getId())
                    ->generateUrl();
                $this->mercurePublisher->publishToMultipleUsers(["title" => "Interview", "message" => "You have a new interview session at", "date" => $interviewDate, "url" => $url], $evaluators);
                return new JsonResponse(['success' => true, 'id' => $interview->getId()], Response::HTTP_OK);

            } catch (\Exception $e) {
                return new JsonResponse(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }elseif ($request->isMethod("GET")){
            $interviews = $this->entityManager->getRepository(Interview::class)->findAll();
            $data = array_map(function ($interview) {
                return [
                    'id' => $interview->getId(),
                    'start' => $interview->getInterviewDate()->format('Y-m-d\TH:i'),
                    'end' => $interview->getInterviewDate()->format('Y-m-d\TH:i'),
                    'title' => $interview->getInterviewLocation(),
                    'location' => $interview->getInterviewLocation(),
                    'candidate' => $interview->getCandidate()->getFullName(),
                    'evaluators' => array_map(function ($evaluator) {
                        return $evaluator->getFullName();
                    }, $interview->getEvaluators()->toArray())
                ];
            }, $interviews);

            return new JsonResponse($data);
        }
        return new JsonResponse(['success' => false, 'message' => 'Method not allowed'], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    #[Route('/api/interviews/{id}', name: 'api_delete_interview', methods: ["DELETE"])]
    public function deleteInterview(Interview $interview): JsonResponse
    {
        try {
            $command = new DeleteInterviewCommand($interview, $this->interviewService, $this->messageBus);
            $this->commandHandler->handle($command);
            return new JsonResponse(['success' => true]);

        }catch (\Exception $e) {
            return new JsonResponse(['success' => false,'message' => 'An error occurred: '. $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
//    #[Route('/api/upcoming-interviews', name: 'api_upcoming_interviews', methods: ["GET"])]
//
//    public function getUpcomingInterviews(): JsonResponse
//    {
//        $interviews = $this->entityManager->getRepository(Interview::class)->findUpcomingInterviews();
//        $formattedInterviews = [];
//
//        foreach ($interviews as $interview) {
//            $formattedInterviews[] = [
//                'id' => $interview->getId(),
//                'title' => $interview->getCandidate()->getFullName(),
//                'start' => $interview->getDateTime()->format('Y-m-d\TH:i:s'),
//                'location' => $interview->getLocation(),
//            ];
//        }
//
//        return new JsonResponse($formattedInterviews);
//    }

}
