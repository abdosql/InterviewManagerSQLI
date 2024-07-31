<?php

namespace App\Controller\Admin\Abstract;

use App\Candidate\Command\Handler\DefaultCommandHandler;
use App\User\Command\CreateUserCommand;
use App\User\Command\DeleteUserCommand;
use App\User\Command\UpdateUserCommand;
use App\Entity\User;
use App\Manager\UserCredentialManager;
use App\Services\Impl\UserService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;

Abstract class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private UserService $userService,
        private UserCredentialManager $credentialManager,
        private DefaultCommandHandler $commandHandler
    )
    {}
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield FormField::addPanel('Personal information');
        yield TextField::new("fullName", "Full Name")->hideOnForm();
        yield TextField::new('firstName', "First name")
            ->onlyOnForms(true)
        ;
        yield TextField::new('lastName', "Last name")
            ->onlyOnForms(true)
        ;
        yield FormField::addPanel('Contact Information');
        yield TextField::new('phone', "Phone Number");
        yield TextField::new('email', "Email");
    }

    /**
     * @throws ExceptionInterface
     */
    private function createOrUpdateUser(User $user): void
    {
        try {
            if (!$user->getId()){
                $command = new CreateUserCommand($user, $this->userService, $this->credentialManager);
            }else{
                $command = new UpdateUserCommand($user, $this->userService);
            }
            $this->commandHandler->handle($command);
        } catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch command to message bus.', 0, $e);
        }
    }

    /**
     * @param User $user
     * @throws ExceptionInterface
     */
    public function deleteUser(User $user): void
    {
        try {
            $command = new DeleteUserCommand($user, $this->userService);
            $this->commandHandler->handle($command);
        }catch (TransportException $e){
            throw new \RuntimeException('Failed to dispatch command to message bus.', 0, $e);
        }
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $entityInstance
     * @throws ExceptionInterface
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->createOrUpdateUser($entityInstance);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $entityInstance
     * @throws ExceptionInterface
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->createOrUpdateUser($entityInstance);
    }

    /**
     * @throws ExceptionInterface
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->deleteUser($entityInstance);
    }
}
