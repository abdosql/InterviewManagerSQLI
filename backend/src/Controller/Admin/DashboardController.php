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
    public function __construct()
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
            return $this->redirect($adminUrlGenerator->setController(CandidateCrudController::class)->generateUrl());
        }
        return $this->redirect($adminUrlGenerator->setController(InterviewCrudController::class)->generateUrl());


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
