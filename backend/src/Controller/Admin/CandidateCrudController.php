<?php

/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */
namespace App\Controller\Admin;

use App\Command\CandidateCommands\CreateCandidateCommand;
use App\Command\CandidateCommands\UpdateCandidateCommand;
use App\EasyAdmin\Fields\ResumeUploadField;
use App\Entity\Candidate;
use App\Handler\CommandHandler\CandidateCommandHandlers\CreateCandidateCommandHandler;
use App\Handler\CommandHandler\CandidateCommandHandlers\UpdateCandidateCommandHandler;
use App\Services\FileUploadServiceInterface;
use App\Services\Impl\CandidateService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;

class CandidateCrudController extends AbstractCrudController
{


    public function __construct(
        private CreateCandidateCommandHandler $createCandidateCommandHandler,
        private UpdateCandidateCommandHandler $updateCandidateCommandHandler,
        private FileUploadServiceInterface $resumeUploadService,
        private CandidateService $candidateService,
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
        $resumeField = ResumeUploadField::new('resume.filePath', 'Resume')
            ->onlyOnForms();

        if ($pageName == CRUD::PAGE_NEW){
            $resumeField->setRequired(true);
        }elseif ($pageName == CRUD::PAGE_EDIT){
            $resumeField->setRequired(false);
        }
        yield $resumeField;
        yield TextField::new('address');
    }

    /**
     * @throws ExceptionInterface
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->createOrUpdateCandidate($entityInstance);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $entityInstance
     * @throws ExceptionInterface
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->createOrUpdateCandidate($entityInstance);
    }

    /**
     * @param Candidate $candidate
     * @throws ExceptionInterface
     * @throws \Exception
     */
    private function createOrUpdateCandidate(Candidate $candidate): void
    {
        try {
            $file = $this->getContext()->getRequest()->files->get('Candidate')['resume_filePath'] ?? null;
            if ($candidate->getId() === null) {
                if (!$file instanceof UploadedFile){
                    throw new \Exception(   'Resume file is required for new Candidate');
                }
                $candidate->getResume()->setFilePath($this->resumeUploadService->handleFileUpload($file));
                $command = new CreateCandidateCommand($candidate, $this->candidateService);
                $this->createCandidateCommandHandler->handle($command);
            } else {

                if(isset($file)){

                    if (!$file instanceof UploadedFile){
                        throw new \Exception(   'Resume file is required for new Candidate');
                    }
                    $candidate->getResume()->setFilePath($this->resumeUploadService->handleFileUpload($file));
                };
                $command = new UpdateCandidateCommand($candidate, $this->candidateService);
                $this->updateCandidateCommandHandler->handle($command);
            }
        } catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch command to message bus.', 0, $e);
        }
    }
}
