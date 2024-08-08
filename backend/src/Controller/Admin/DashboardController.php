<?php

namespace App\Controller\Admin;

use App\Entity\Candidate;
use App\Entity\Evaluator;
use App\Entity\HRManager;
use App\Entity\Interview;
use App\Persister\EntityPersisterInterface;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
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
        #[Autowire(service: "App\Services\Impl\CandidateService")]
        private readonly EntityPersistenceServiceInterface $candidateService,
        #[Autowire(service: "App\Services\Impl\EvaluatorService")]
        private readonly EntityPersistenceServiceInterface $evaluatorService
    )
    {
    }

    /**
     * @return Response
     */
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        //It will be changed after using the api side
        $candidates = $this->candidateService->findAllEntities();
        $evaluators = $this->evaluatorService->findAllEntities();
        return $this->render('admin/dashboard.html.twig',
            [
                'candidates' => $candidates,
                'evaluators' => $evaluators,
            ]
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Html');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        if($this->isGranted('ROLE_HR')){
            yield MenuItem::linkToCrud('Candidates', 'fa-solid fa-user-tie', Candidate::class)->setPermission("ROLE_HR");
            yield MenuItem::linkToCrud('Evaluators', 'fa-solid fa-user-secret', Evaluator::class)->setPermission("ROLE_HR");
        }
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('HR Manager', 'fa-solid fa-people-line', HRManager::class)->setPermission("ROLE_ADMIN");
        }
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
