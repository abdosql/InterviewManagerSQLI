<?php

declare(strict_types=1);

namespace App\Controller\Admin\Abstract;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractCustomCrudController extends AbstractCrudController
{
    public function __construct(private readonly AdminUrlGenerator $adminUrlGenerator)
    {
    }
    abstract protected function getAllItems(): array;
    abstract protected function getItemById($id): ?object;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $items = $this->getAllItems();
        $em = $this->container->get('doctrine')->getManagerForClass($entityDto->getFqcn());
        $hydratorClassName = 'CustomItemHydrator' . uniqid();
        $hydratorClass = new class($em, $items) extends AbstractHydrator {
            private array $items;

            public function __construct(EntityManagerInterface $em, array $items) {
                parent::__construct($em);
                $this->items = $items;
            }

            protected function hydrateAllData(): array {
                return array_map(function($item) {
                    return ['result' => $item];
                }, $this->items);
            }
        };

        $config = $em->getConfiguration();
        $config->addCustomHydrationMode($hydratorClassName, get_class($hydratorClass));

        $qb = $em->createQueryBuilder();
        $qb->select('entity')
            ->from($entityDto->getFqcn(), 'entity')
            ->setMaxResults(1);

        $qb->getQuery()->setHydrationMode($hydratorClassName);

        return $qb;
    }

    public function detail(AdminContext $context): Response
    {
        $id = $context->getRequest()->query->get('entityId');

        try {
            $entity = $this->getItemById($id);
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred while fetching the entity: ' . $e->getMessage());
            return $this->redirect($this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
        }

        if (!$entity) {
            $this->addFlash('error', 'Entity not found.');
            return $this->redirect($this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
        }

        $context->getEntity()->setInstance($entity);

        $responseParameters = parent::detail($context);
        $templateName = "@EasyAdmin/".$responseParameters->get('templateName').".html.twig";

        return $this->render($templateName, $responseParameters->all());
    }
}
