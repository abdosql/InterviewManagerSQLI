<?php

namespace App\Controller\Admin;

use App\Candidate\Query\GetAllCandidates;
use App\Candidate\Query\ItemsQueryInterface as CandidateItemsQueryInterface;
use App\Entity\InterviewStatus;
use App\Interview\Query\GetAllInterviews;
use App\Interview\Query\ItemsQueryInterface as InterviewItemsQueryInterface;
use App\Entity\Candidate;
use App\Entity\Evaluator;
use App\Entity\HRManager;
use App\Entity\Interview;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        #[Autowire(service: GetAllCandidates::class)]
        private readonly CandidateItemsQueryInterface $getAllCandidates,
        #[Autowire(service: GetAllInterviews::class)]
        private readonly InterviewItemsQueryInterface $getAllInterviews
    )
    {
    }

    /**
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        if ($this->isGranted("ROLE_HR_MANAGER")){
            $interviewStats = $this->getInterviewStats();
            $candidateStats = $this->getCandidateStats();

            return $this->render('admin/dashboard.html.twig', [
                'interviewStats' => $interviewStats,
                'candidateStats' => $candidateStats,
            ]);
        }
        return $this->redirect($adminUrlGenerator->setController(InterviewCrudController::class)->generateUrl());


    }

    private function getInterviewStats(): array
    {
        $interviews = $this->getAllInterviews->findItems();
        $statuses = [InterviewStatus::SCHEDULED, InterviewStatus::IS_FAILED, InterviewStatus::IS_PASSED, InterviewStatus::IN_PROGRESS];
        $stats = array_fill_keys($statuses, 0);

        foreach ($interviews as $interview) {
            $currentStatus = $interview->getInterviewStatuses()->last()->getStatus();
            if (isset($stats[$currentStatus])) {
                $stats[$currentStatus]++;
            }
        }

        return $stats;
    }

    private function getCandidateStats(): array
    {
        $candidates = $this->getAllCandidates->findItems();
        $acceptedCount = 0;
        $failedCount = 0;
        $inProgressCount = 0;

        foreach ($candidates as $candidate) {
            $latestStatus = $this->getLatestInterviewStatus($candidate);

            if ($latestStatus === InterviewStatus::IS_PASSED) {
                $acceptedCount++;
            } elseif ($latestStatus === InterviewStatus::IS_FAILED) {
                $failedCount++;
            } elseif ($latestStatus === InterviewStatus::IN_PROGRESS || $latestStatus === InterviewStatus::SCHEDULED) {
                $inProgressCount++;
            }
        }

        return [
            'accepted' => $acceptedCount,
            'failed' => $failedCount,
            'inProgress' => $inProgressCount,
        ];
    }

    private function getLatestInterviewStatus(Candidate $candidate): ?string
    {
        $latestInterview = $candidate->getInterviews()->last();
        if ($latestInterview) {
            $latestStatus = $latestInterview->getInterviewStatuses()->last();
            return $latestStatus ? $latestStatus->getStatus() : null;
        }
        return null;
    }
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Html');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        if($this->isGranted('ROLE_HR_MANAGER')){
            yield MenuItem::linkToCrud('Candidates', 'fa-solid fa-user-tie', Candidate::class)
                ->setPermission("ROLE_EVALUATOR");
            yield MenuItem::linkToCrud('Evaluators', 'fa-solid fa-users', Evaluator::class)
                ->setPermission("ROLE_HR_MANAGER");
        }
        yield MenuItem::subMenu('Interviews', 'fa-solid fa-clipboard-question')->setSubItems([
            MenuItem::linkToRoute('Calendar', 'fa-regular fa-calendar-days', "interview_calendar"),
            MenuItem::LinkToCrud('List', 'fa fa-list', Interview::class),
        ])
            ->setPermission("ROLE_EVALUATOR");
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('HR Manager', 'fa-solid fa-people-line', HRManager::class)
                ->setPermission("ROLE_ADMIN");
        }
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
