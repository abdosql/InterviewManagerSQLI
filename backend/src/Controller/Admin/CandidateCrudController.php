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
use App\Candidate\Query\FindCandidate;
use App\Candidate\Query\GetAllCandidates;
use App\EasyAdmin\Fields\ResumeUploadField;
use App\Entity\Candidate;
use App\File\FileUploaderInterface;
use App\File\Uploader\MinioUploader;
use App\Notification\MercurePublisher;
use App\Services\Impl\CandidateService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CandidateCrudController extends AbstractCrudController
{


    public function __construct(
        private readonly CommandHandlerInterface $commandHandler,
        #[Autowire(service: MinioUploader::class)]
        private readonly FileUploaderInterface   $resumeUploadService,
        private readonly CandidateService        $candidateService,
        private readonly MessageBusInterface     $messageBus,
        private readonly FindCandidate           $findCandidateQuery,
        private readonly GetAllCandidates        $allCandidates,
        private readonly AdminUrlGenerator       $adminUrlGenerator,
        private readonly MercurePublisher  $mercurePublisher,
    )
    {}

    public static function getEntityFqcn(): string
    {
        return Candidate::class;
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
        $entities = $this->allCandidates->findItems();
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
        $entityName = $crud->getEntityFqcn();
        $actions = $crud->getActionsConfig()->getActions();
//        dd($actions, $entityName);
        return $this->render('@EasyAdmin/crud/index.html.twig', [
            'entities' => $entities,
            'fields' => $fieldMetadata,
            'actions' => $actions,
            'entityName' => $entityName,
            'entityLabel' => $entityLabel,
        ]);
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
        yield TextField::new('email', 'Email Address');
        $resumeField = ResumeUploadField::new('resume.filePath', 'Resume')
            ->onlyOnForms();

        if ($pageName == CRUD::PAGE_NEW){
            $resumeField->setRequired(true);
        }elseif ($pageName == CRUD::PAGE_EDIT){
            $resumeField->setRequired(false);
        }
        yield $resumeField;
        yield TextField::new('address', "Address");
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
     * @throws NotFoundExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function detail(AdminContext $context): Response
    {
        $id = $context->getRequest()->query->get('entityId');

        try {
            $candidate = $this->findCandidateQuery->findItem($id);
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred while fetching the candidate: ' . $e->getMessage());
            return $this->redirect($this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
        }

        if (!$candidate) {
            $this->addFlash('error', 'Candidate not found.');
            return $this->redirect($this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
        }

        $context->getEntity()->setInstance($candidate);

        $responseParameters = parent::detail($context);
        $templateName = "@EasyAdmin/".$responseParameters->get('templateName').".html.twig";

        return $this->render($templateName, $responseParameters->all());
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
        $this->mercurePublisher->publish(["message" => "You Have a new interview"], $this->getUser());
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
