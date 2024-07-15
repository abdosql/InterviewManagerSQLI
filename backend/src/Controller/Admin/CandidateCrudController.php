<?php

namespace App\Controller\Admin;

use App\Command\CandidateCommands\CreateCandidateCommand;
use App\Command\CandidateCommands\UpdateCandidateCommand;
use App\CommandHandler\CandidateCommandHandlers\CreateCandidateCommandHandler;
use App\CommandHandler\CandidateCommandHandlers\UpdateCandidateCommandHandler;
use App\EasyAdmin\Fields\CVUploadField;
use App\Entity\Candidate;
use App\Services\Interfaces\FileUploadServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

class CandidateCrudController extends AbstractCrudController
{


    public function __construct(
        private FileUploadServiceInterface $resumeUploadService,
        private CreateCandidateCommandHandler $createCandidateCommandHandler,
        private UpdateCandidateCommandHandler $updateCandidateCommandHandler,

    )
    {}

    public static function getEntityFqcn(): string
    {
        return Candidate::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield FormField::addPanel('Personal information');
        yield TextField::new('firstName')
            ->setRequired(true)
        ;
        yield TextField::new('lastName')
            ->setRequired(true)
        ;
        yield FormField::addPanel('Contact Information');
        yield TextField::new('phone', "Phone Number");
        yield TextField::new('email');
        yield CVUploadField::new('resume.filePath', 'Resume')
            ->onlyOnForms()
        ;
        yield TextField::new('address');


    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $entityInstance
     * @throws ExceptionInterface
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->createOrUpdateCandidate($entityManager,$entityInstance);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param Candidate $candidate
     * @throws ExceptionInterface
     */
    private function createOrUpdateCandidate(EntityManagerInterface $entityManager, Candidate $candidate): void
    {
        try {
            $filePath = $this->resumeUploadService->handleFileUpload(
                $this->getContext()->getRequest()->files->get('Candidate')['resume_filePath']
            );
            $candidate->getResume()->setFilePath($filePath);

            if ($candidate->getId() === null) {
                $command = new CreateCandidateCommand($entityManager, $candidate);
                $this->createCandidateCommandHandler->handle($command);
            } else {
                $command = new UpdateCandidateCommand($entityManager, $candidate);
                $this->updateCandidateCommandHandler->handle($command);
            }
        } catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch command to message bus.', 0, $e);
        }
    }

}
