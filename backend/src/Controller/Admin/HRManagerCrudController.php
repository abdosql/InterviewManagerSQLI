<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Abstract\UserCrudController;
use App\Entity\HRManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class HRManagerCrudController extends UserCrudController
{
    public static function getEntityFqcn(): string
    {
        return HRManager::class;
    }

//    /**
//     * @throws NotFoundExceptionInterface
//     * @throws TransportExceptionInterface
//     * @throws ServerExceptionInterface
//     * @throws RedirectionExceptionInterface
//     * @throws ContainerExceptionInterface
//     * @throws ClientExceptionInterface
//     */
//    public function index(AdminContext $context): Response
//    {
//        $crud = $context->getCrud();
//        $entities = $this->getUsersByType->findItems(["dType" => "rh"]);
//        $fields = $this->configureFields(Crud::PAGE_INDEX);
//        $fieldMetadata = [];
//        $entityLabel = $crud->getEntityLabelInSingular();
//
//        foreach ($fields as $field) {
//            if (!$field->getAsDto()->getDisplayedOn()->has('index')) {
//                continue;
//            }
//            $fieldMetadata[] = [
//                'label' => $field->getAsDto()->getLabel(),
//                'property' => $field->getAsDto()->getProperty(),
//            ];
//        }
//
//        $actions = $crud->getActionsConfig()->getActions();
////        dd($actions, $entityName);
//        return $this->render('@EasyAdmin/crud/index.html.twig', [
//            'entities' => $entities,
//            'fields' => $fieldMetadata,
//            'actions' => $actions,
//            'entityLabel' => $entityLabel,
//        ]);
//    }
    public function configureFields(string $pageName): iterable
    {
        yield from parent::configureFields($pageName);
        yield TextField::new('department', "Department");
        yield TextField::new('position', "Position");
    }

}
