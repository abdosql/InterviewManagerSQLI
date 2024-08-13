<?php

/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */
namespace App\Controller\Admin;

use App\Candidate\Command\CreateCandidateCommand;
use App\Candidate\Command\DeleteCandidateCommand;
use App\Candidate\Command\Handler\CommandHandlerInterface;
use App\Candidate\Command\UpdateCandidateCommand;
use App\EasyAdmin\Fields\ResumeUploadField;
use App\Entity\Candidate;
use App\File\FileUploaderInterface;
use App\File\Uploader\DefaultFileUploader;
use App\File\Uploader\MinioUploader;
use App\Services\Impl\CandidateService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

class CandidateCrudController extends AbstractCrudController
{


    public function __construct(
        private CommandHandlerInterface $commandHandler,
        #[Autowire(service: MinioUploader::class)]
        private FileUploaderInterface $resumeUploadService,
        private CandidateService $candidateService,
        private MessageBusInterface $messageBus,
    )
    {}

    public static function getEntityFqcn(): string
    {
        return Candidate::class;
    }

    public function configureFields(string $pageName): iterable
    {
//        dd($this->candidateService->findDocumentByEntity(1));
        yield IdField::new('id')->hideOnForm();
        yield FormField::addPanel('Personal information');
        yield TextField::new("fullName", "Full Name")->hideOnForm();
        yield TextField::new('firstName')
            ->onlyOnForms(true)
        ;
        yield TextField::new('lastName')
            ->onlyOnForms(true)
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
     * @param EntityManagerInterface $entityManager
     * @param $entityInstance
     * @throws \Exception
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->createOrUpdateCandidate($entityInstance);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $entityInstance
     * @throws \Exception
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->createOrUpdateCandidate($entityInstance);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $entityInstance
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->deleteCandidate($entityInstance);
    }

    /**
     * @param Candidate $candidate
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
                $candidate->getResume()->setFilePath($this->resumeUploadService->upload($file));
                $command = new CreateCandidateCommand($candidate, $this->candidateService, $this->messageBus);
            } else {
                if(isset($file)){
                    if (!$file instanceof UploadedFile){
                        throw new \Exception(   'Resume file is required for new Candidate');
                    }
                    $candidate->getResume()->setFilePath($this->resumeUploadService->upload($file));
                };
                $command = new UpdateCandidateCommand($candidate, $this->candidateService, $this->messageBus);
            }
            $this->commandHandler->handle($command);
        } catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch command to message bus.', 0, $e);
        }
    }

    /**
     * @param Candidate $candidate
     */
    public function deleteCandidate(Candidate $candidate): void
    {
        try {
            $command = new DeleteCandidateCommand($candidate, $this->candidateService, $this->messageBus);
            $this->commandHandler->handle($command);
        }catch (TransportException $e){
            throw new \RuntimeException('Failed to dispatch command to message bus.', 0, $e);
        }
    }
}
