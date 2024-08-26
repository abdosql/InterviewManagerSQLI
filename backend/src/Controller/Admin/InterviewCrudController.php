<?php

namespace App\Controller\Admin;

use App\Candidate\Query\FindCandidate;
use App\Entity\Interview;
use App\Form\InterviewType;
use App\Interview\Command\CreateInterviewCommand;
use App\Interview\Command\DeleteInterviewCommand;
use App\Interview\Command\Handler\CommandHandlerInterface;
use App\Interview\Query\GetAllInterviews;
use App\Interview\Query\GetAllUpcomingInterviews;
use App\Publisher\MercurePublisher;
use App\Publisher\PublisherInterface;
use App\Services\Impl\InterviewService;
use App\User\Query\FindUser;
use App\User\Query\GetUsersByIds;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
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
        private readonly GetUsersByIds $getUsersByIds,
        private readonly PublisherInterface  $mercurePublisher,
        private readonly AdminUrlGenerator       $adminUrlGenerator,
        private readonly GetAllInterviews $getAllInterviews,
        private readonly GetAllUpcomingInterviews $allUpcomingInterviews


    )
    {
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function index(AdminContext $context): Response
    {
        $crud = $context->getCrud();
        $entities = $this->getAllInterviews->findItems();
        $fields = $this->configureFields(Crud::PAGE_INDEX);
        $fieldMetadata = [];
        $entityLabel = $crud->getEntityLabelInSingular();

        foreach ($fields as $field) {
            if (!$field->getAsDto()->getDisplayedOn()->has('index')) {
                continue;
            }
            $fieldMetadata[] = [
                'label' => $field->getAsDto()->getLabel(),
                'property' => $field->getAsDto()->getProperty(),
            ];
        }

        $entityName = $crud->getEntityFqcn();
        $actions = $crud->getActionsConfig()->getActions();
//        dd($actions, $entityName);
        return $this->render('@EasyAdmin/crud/index.html.twig', [
            'entities' => $entities,
            'fields' => $fieldMetadata,
            'actions' => $actions,
            'entityName' => $entityName,
            'entityLabel' => $entityLabel,
        ]);
    }


    public static function getEntityFqcn(): string
    {
        return Interview::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action
                    ->setIcon('fa fa-edit');
            })->disable(Action::DELETE, Action::EDIT, Action::NEW);


        return $actions;
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('interviewLocation', "Location")->hideOnForm();
        yield DateField::new('interviewDate', "Date and Time")->hideOnForm()
            ->formatValue(function ($value) {
                return $value->format("Y-m-d H:i:s");
            });

        yield AssociationField::new('candidate', 'Candidates')
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


    /**
     * @throws TransportExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ServerExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/interview/calendar', name: 'interview_calendar', methods: ["GET"])]

    public function viewCalendar(): Response
    {
        $interview = new Interview();
        $form = $this->createForm(InterviewType::class, $interview);

        return $this->render('interview/index.html.twig', [
            'form' => $form->createView(),
            'upcomingInterviews' => $this->getUpcomingInterviews(),
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
            $interviews = $this->getAllInterviews->findItems();
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

    /**
     * @throws NotFoundExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getUpcomingInterviews(): array
    {
        $interviews = $this->allUpcomingInterviews->findItems();
        $formattedInterviews = [];

        foreach ($interviews as $interview) {
            $formattedInterviews[] = [
                'candidate' => $interview->getCandidate()->getFullName(),
                'date' => $interview->getInterviewDate()->format('Y-m-d'),
                'evaluators' => $interview->getEvaluators(),
                'time' => $interview->getInterviewDate()->format('H:i'),
                'location' => $interview->getInterviewLocation(),
            ];
        }
        return $formattedInterviews;
    }

}
