<?php

namespace App\Controller\Admin;

use App\Command\CandidateCommands\CreateCandidateCommand;
use App\CommandHandler\CandidateCommandHandlers\CreateCandidateCommandHandler;
use App\EasyAdmin\Fields\CVUploadField;
use App\Entity\Candidate;
use App\Services\Interfaces\FileUploadServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

class CandidateCrudController extends AbstractCrudController
{

    public function __construct(
        private FileUploadServiceInterface $resumeUploadService,
        private CreateCandidateCommandHandler $createCandidateCommandHandler
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
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleResumeUpload($entityInstance);
    }

    /**
     * @param Candidate $candidate
     * @throws
     */
    private function handleResumeUpload(Candidate $candidate): void
    {
        try {
            $filePath = $this->resumeUploadService->handleFileUpload(
                $this->getContext()->getRequest()->files->get('Candidate')['resume_filePath']
            );
            $command = new CreateCandidateCommand(
                $candidate->getFirstName(),
                $candidate->getLastName(),
                $candidate->getPhone(),
                $candidate->getEmail(),
                $candidate->getAddress(),
                $filePath
            );
            $this->createCandidateCommandHandler->handle($command);
        } catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch command to message bus.', 0, $e);
        }
    }

}
