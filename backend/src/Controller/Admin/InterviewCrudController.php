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
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
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

    public static function getEntityFqcn(): string
    {
        return Interview::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */

    public function index(AdminContext $context): Response
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

        if (empty($data['date']) || empty($data['location']) || empty($data['candidate']) || empty($data['evaluator'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Some fields are missing'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $candidate = $this->entityManager->getRepository(Candidate::class)->find($data['candidate']);
            $evaluator = $this->entityManager->getRepository(Evaluator::class)->find($data['evaluator'][0]);

            if (!$candidate || !$evaluator) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Invalid candidate or evaluator',
                    ], Response::HTTP_BAD_REQUEST);
            }

            $interviewDate = \DateTime::createFromFormat('Y-m-d\TH:i', $data['date']);
            if (!$interviewDate) {
                return new JsonResponse(['success' => false, 'message' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
            }

            $interview = new Interview();
            $interview
                ->setInterviewDate($interviewDate)
                ->setInterviewLocation($data['location'])
                ->setCandidate($candidate)
                ->setEvaluator($evaluator)
                ->setHrManager($this->getUser());
            ;

            $command = new CreateInterviewCommand($interview, $this->interviewService, $this->messageBus);
            $this->commandHandler->handle($command);

            return new JsonResponse(['success' => true, 'id' => $interview->getId()], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/interviews/{id}', name: 'api_delete_interview', methods: ["DELETE"])]
    public function deleteInterview(Interview $interview): JsonResponse
    {
        $this->entityManager->remove($interview);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true]);
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

    private function getFormErrors($form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }
        return $errors;
    }
}
