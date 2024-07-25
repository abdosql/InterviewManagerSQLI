<?php
declare(strict_types=1);
namespace App\Controller\Admin;

use App\Command\UserCommands\CreateUserCommand;
use App\Command\UserCommands\DeleteUserCommand;
use App\Command\UserCommands\UpdateUserCommand;
use App\Entity\Candidate;
use App\Entity\Evaluator;
use App\Entity\User;
use App\Handler\CommandHandler\UserCommandHandlers\CreateUserCommandHandler;
use App\Handler\CommandHandler\UserCommandHandlers\DeleteUserCommandHandler;
use App\Handler\CommandHandler\UserCommandHandlers\UpdateUserCommandHandler;
use App\Services\Impl\UserService;
use App\Services\Manager\UserCredentialManager;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;

class EvaluatorCrudController extends AbstractCrudController
{
    public function __construct(
        private UserService $userService,
        private UserCredentialManager $credentialManager,
        private CreateUserCommandHandler $createUserCommandHandler,
        private UpdateUserCommandHandler $updateUserCommandHandler,
        private DeleteUserCommandHandler $deleteUserCommandHandler,
    )
    {}
    public static function getEntityFqcn(): string
    {
        return Evaluator::class;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $entityInstance
     * @throws ExceptionInterface
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->createOrUpdateEvaluator($entityInstance);
    }

    /**
     * @throws ExceptionInterface
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->createOrUpdateEvaluator($entityInstance);
    }

    /**
     * @throws ExceptionInterface
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->deleteEvaluator($entityInstance);
    }

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
        yield TextField::new('specialization', "Specialization");
    }

    /**
     * @throws ExceptionInterface
     */
    private function createOrUpdateEvaluator(Evaluator $evaluator): void
    {
        try {
            if (!$evaluator->getId()){
                $command = new CreateUserCommand($evaluator, $this->userService, $this->credentialManager);
                $this->createUserCommandHandler->handle($command);
            }else{
                $command = new UpdateUserCommand($evaluator, $this->userService);
                $this->updateUserCommandHandler->handle($command);
            }
        } catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch command to message bus.', 0, $e);
        }
    }

    /**
     * @param User $evaluator
     * @throws ExceptionInterface
     */
    public function deleteEvaluator(User $evaluator): void
    {
        try {
            $command = new DeleteUserCommand($evaluator, $this->userService);
            $this->deleteUserCommandHandler->handle($command);
        }catch (TransportException $e){
            throw new \RuntimeException('Failed to dispatch command to message bus.', 0, $e);
        }
    }
}
