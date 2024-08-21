<?php
declare(strict_types=1);
namespace App\Controller\Admin;
use App\Candidate\Command\Handler\DefaultCommandHandler;
use App\Controller\Admin\Abstract\UserCrudController;
use App\Entity\Evaluator;
use App\Manager\UserCredentialManager;
use App\Services\Impl\UserService;
use App\User\Query\FindUser;
use App\User\Query\GetUsersByRole;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class EvaluatorCrudController extends UserCrudController
{
    public function __construct(UserService $userService, UserCredentialManager $credentialManager, DefaultCommandHandler $commandHandler, MessageBusInterface $messageBus, FindUser $findUsereQuery, GetUsersByRole $getUsersByRole, AdminUrlGenerator $adminUrlGenerator)
    {
        parent::__construct($userService, $credentialManager, $commandHandler, $messageBus, $findUsereQuery, $getUsersByRole, $adminUrlGenerator);
    }

    public static function getEntityFqcn(): string
    {
        return Evaluator::class;
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
        $entities = $this->getUsersByRole->findItems(["dType" => "evaluator"]);
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

        $actions = $crud->getActionsConfig()->getActions();
//        dd($actions, $entityName);
        return $this->render('@EasyAdmin/crud/index.html.twig', [
            'entities' => $entities,
            'fields' => $fieldMetadata,
            'actions' => $actions,
            'entityLabel' => $entityLabel,
        ]);
    }
    public function configureFields(string $pageName): iterable
    {
        yield from parent::configureFields($pageName);
        yield TextField::new('specialization', "Specialization");
    }


}
