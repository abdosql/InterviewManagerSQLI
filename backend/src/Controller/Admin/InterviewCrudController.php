<?php

namespace App\Controller\Admin;

use App\AI\Facade\AIFacade;
use App\Candidate\Query\FindCandidate;
use App\Entity\Interview;
use App\Entity\InterviewStatus;
use App\Form\Type\FroalaEditorType;
use App\Form\Type\InterviewType;
use App\Interview\Command\AddInterviewStatusCommand;
use App\Interview\Command\CreateInterviewCommand;
use App\Interview\Command\DeleteInterviewCommand;
use App\Interview\Command\Handler\CommandHandlerInterface;
use App\Interview\Query\FindInterview;
use App\Interview\Query\GetAllInterviews;
use App\Interview\Query\GetAllUpcomingInterviews;
use App\Interview\Query\ItemQueryInterface;
use App\Publisher\PublisherInterface;
use App\Services\AIInterviewService;
use App\Services\Impl\InterviewService;
use App\Services\Impl\InterviewStatusService;
use App\User\Query\GetUsersByIds;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use function Symfony\Component\Clock\now;

class InterviewCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly CommandHandlerInterface $commandHandler,
        private readonly MessageBusInterface $messageBus,
        private readonly InterviewService $interviewService,
        private readonly FindCandidate $findCandidate,
        private readonly GetUsersByIds $getUsersByIds,
        private readonly PublisherInterface  $mercurePublisher,
        private readonly AdminUrlGenerator       $adminUrlGenerator,
        private readonly GetAllInterviews $getAllInterviews,
        private readonly GetAllUpcomingInterviews $allUpcomingInterviews,
        #[Autowire(service: FindInterview::class)]
        private readonly ItemQueryInterface $interviewItemQuery,
        private readonly AIInterviewService $AIInterviewService

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

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->overrideTemplates([
            'crud/detail' => 'admin/interview/detail.html.twig',
        ]);
    }

    public static function getEntityFqcn(): string
    {
        return Interview::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)->setPermission(Action::DELETE, "ROLE_HR_MANAGER")
            ->remove(Crud::PAGE_INDEX, Action::EDIT)->setPermission(Action::EDIT, "ROLE_HR_MANAGER")
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action
                    ->setIcon('fa fa-eye')
                    ;
            });


        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Candidate information')->onlyOnDetail();
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('interviewLocation', "Location")->hideOnForm();
        yield DateField::new('interviewDate', "Date and Time")->hideOnForm()
            ->formatValue(function ($value) {
                return $value->format("Y-m-d H:i:s");
            });

        yield AssociationField::new('candidate', 'Candidate')
            ->formatValue(function ($value) {
                $resumePath = $value->getResume()->getFilePath();
                return $value->getFirstName() . ' ' . $value->getLastName(). '<a class="btn btn-sm btn-secondary ms-3" href='.$resumePath.'>Download Resume<a/>';
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

    public function detail(AdminContext $context): Response
    {
        $keyValueStore = parent::detail($context);
//        $entityId = $context->getRequest()->query->get("entityId");
//        $interviewInstance = $this->interviewItemQuery->findItem($entityId);
//        dd($interviewInstance);

        $parameters = $keyValueStore->all();
        // Create an empty Froala form
        $form = $this->createForm(FroalaEditorType::class);

        // Add the form view to the parameters
        $parameters['froalaForm'] = $form->createView();

        // Render the response with the additional parameter
        return $this->render($context->getTemplatePath('crud/detail'), $parameters);
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
                $interviewStatus = new InterviewStatus();
                $interviewStatus
                    ->setStatus('SCHEDULED')
                    ->setInterview($interview)
                    ->setStatusDate(now())
                ;
                $interview
                    ->setInterviewDate($interviewDate)
                    ->setInterviewLocation($data['location'])
                    ->setCandidate($candidate)
                    ->setHrManager($this->getUser())
                    ->addInterviewStatus($interviewStatus)

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
            $interviews = $this->isGranted("ROLE_HR_MANAGER")
                ? $this->getAllInterviews->findItems()
                : $this->getAllInterviews->findItems(['userId' => $this->getUser()->getId()])
            ;
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

    #[IsGranted("ROLE_HR_MANAGER")]
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
        $interviews = $this->isGranted("ROLE_HR_MANAGER")
            ? $this->allUpcomingInterviews->findItems()
            : $this->allUpcomingInterviews->findItems(['userId' => $this->getUser()->getId()])
        ;
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

    /**
     * @throws \DateMalformedStringException
     */
    #[Route('/api/interviewStatus', name: 'api_interview_status', methods: ["POST"])]
    public function addInterviewStatus(Request $request, InterviewStatusService $interviewStatusService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $status = $data['status'];

        $interviewId = $data['interviewId'];
        if (empty($status)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Status missing'],
                Response::HTTP_BAD_REQUEST
            );
        }
        if (empty($interviewId)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Missing interview ID'],
                Response::HTTP_BAD_REQUEST
            );
        }
        $interview = $this->interviewItemQuery->findItem((int)$interviewId);
        $interviewStatus = new InterviewStatus();
        $interviewStatus
            ->setStatus($status)
            ->setInterview($interview)
            ->setStatusDate(now())
        ;
        $command = new AddInterviewStatusCommand($interviewStatus, $interviewStatusService, $this->messageBus);
        $this->commandHandler->handle($command);
        return new JsonResponse(['status' => 'success', 'id' => $interviewStatus->getId()], Response::HTTP_OK);
    }



    #[Route('/interview/ai-feedback', name: 'interview_ai_feedback')]
    public function getAIFeedback(): Response
    {
        $prompt = "
            {
              'comment': 'The candidate's performance was poor. They didn't seem to know much about the job. Their communication skills were lacking',
              'score': 5/20
            }
        ";
        $aiFeedback = $this->AIInterviewService->generateInterviewFeedback($prompt);

        return new JsonResponse(['aiFeedback' => $aiFeedback]);
    }
}
