<?php
declare(strict_types=1);
namespace App\Controller\Admin;

use App\Command\CandidateCommands\DeleteCandidateCommand;
use App\Command\EvaluatorCommands\CreateEvaluatorCommand;
use App\Command\UserCommands\CreateUserCommand;
use App\Command\UserCommands\UpdateUserCommand;
use App\Entity\Candidate;
use App\Entity\Evaluator;
use App\Handler\CommandHandler\UserCommandHandlers\CreateUserCommandHandler;
use App\Services\Impl\EvaluatorService;
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
        private EvaluatorService $evaluatorService,
        private UserCredentialManager $credentialManager,
        private CreateUserCommandHandler $createUserCommandHandler,
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

    private function createOrUpdateEvaluator(Evaluator $evaluator): void
    {
        try {
            if (!$evaluator->getId()){
                $command = new CreateUserCommand($evaluator, $this->evaluatorService, $this->credentialManager);
                $this->createUserCommandHandler->handle($command);
            }else{
                $command = new UpdateUserCommand();
            }
        } catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch command to message bus.', 0, $e);
        }
    }

    /**
     * @param Candidate $candidate
     * @throws ExceptionInterface
     */
    public function deleteCandidate(Candidate $candidate): void
    {
//        try {
//            $command = new DeleteCandidateCommand($candidate, $this->candidateService, );
//            $this->deleteCandidateCommandHandler->handle($command);
//        }catch (TransportException $e){
//            throw new \RuntimeException('Failed to dispatch command to message bus.', 0, $e);
//        }
    }
}
