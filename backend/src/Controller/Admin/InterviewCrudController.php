<?php

namespace App\Controller\Admin;

use App\Interview\Command\Handler\CommandHandlerInterface;
use App\Entity\Candidate;
use App\Entity\Evaluator;
use App\Entity\Interview;
use App\Form\InterviewType;
use App\Interview\Command\CreateInterviewCommand;
use App\Services\Impl\InterviewService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class InterviewCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private CommandHandlerInterface $commandHandler,
        private MessageBusInterface $messageBus,
        private InterviewService $interviewService
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
            ->disable(Action::DELETE, Action::EDIT);
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

    #[Route('/api/interviews', name: 'api_create_interview', methods: ["post"])]
    public function createInterview(Request $request): JsonResponse
    {
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
            $candidate = $this->entityManager->getRepository(Candidate::class)->find($data['candidate']);
            if (!$candidate) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Invalid candidate',
                ], Response::HTTP_BAD_REQUEST);
            }
            foreach ($data['evaluators'] as $evaluator) {
                $evaluator = $this->entityManager->getRepository(Evaluator::class)->find($evaluator);
                if (!$evaluator) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Invalid candidate or evaluator',
                    ], Response::HTTP_BAD_REQUEST);
                }
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

            return new JsonResponse(['success' => true, 'id' => $interview->getId()], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

//    #[Route('/api/interviews/{id}', name: 'api_delete_interview', methods: ["DELETE"])]
//    public function deleteInterview(Interview $interview): JsonResponse
//    {
//        $this->entityManager->remove($interview);
//        $this->entityManager->flush();
//
//        return new JsonResponse(['success' => true]);
//    }
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
