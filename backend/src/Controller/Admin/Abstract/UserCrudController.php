<?php

namespace App\Controller\Admin\Abstract;

use App\User\Command\Handler\DefaultCommandHandler;
use App\User\Command\CreateUserCommand;
use App\User\Command\DeleteUserCommand;
use App\User\Command\UpdateUserCommand;
use App\Entity\User;
use App\Manager\UserCredentialManager;
use App\Services\Impl\UserService;
use App\User\Query\FindUser;
use App\User\Query\GetUsersByType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

Abstract class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private UserService                $userService,
        private UserCredentialManager      $credentialManager,
        private DefaultCommandHandler      $commandHandler,
        private MessageBusInterface        $messageBus,
        private readonly FindUser          $findUsereQuery,
        protected readonly GetUsersByType  $getUsersByType,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    )
    {}

    /**
     * @throws TransportExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function getAllItems(): array
    {
        return $this->getUsersByType->findItems();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getItemById($id): ?object
    {
        return $this->findUsereQuery->findItem($id);
    }
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield FormField::addPanel('Personal information')->hideOnIndex();
        yield TextField::new("fullName", "Full Name")->hideOnForm();
        yield TextField::new('firstName', "First name")
            ->onlyOnForms(true)
        ;
        yield TextField::new('lastName', "Last name")
            ->onlyOnForms(true)
        ;
        yield FormField::addPanel('Contact Information')->hideOnIndex();
        yield TextField::new('phone', "Phone Number");
        yield TextField::new('email', "Email");
    }

    /**
     * @param User $user
     */
    private function createOrUpdateUser(User $user): void
    {
        try {
            if (!$user->getId()){
                $command = new CreateUserCommand($user, $this->userService, $this->credentialManager, $this->messageBus);
            }else{
                $command = new UpdateUserCommand($user, $this->userService, $this->messageBus);
            }
            $this->commandHandler->handle($command);
        } catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch command to message bus.', 0, $e);
        }
    }
    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action
                    ->setIcon('fa fa-edit');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action
                    ->setIcon('fa fa-trash');
            });

        return $actions;
    }
    /**
     * @param User $user
     */
    public function deleteUser(User $user): void
    {
        try {
            $command = new DeleteUserCommand($user, $this->userService, $this->messageBus);
            $this->commandHandler->handle($command);
        }catch (TransportException $e){
            throw new \RuntimeException('Failed to dispatch command to message bus.', 0, $e);
        }
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->createOrUpdateUser($entityInstance);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->createOrUpdateUser($entityInstance);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $entityInstance
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->deleteUser($entityInstance);
    }
}
