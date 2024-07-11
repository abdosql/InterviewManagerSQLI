<?php

namespace App\Controller\Admin;

use App\EasyAdmin\Fields\CVUploadField;
use App\Entity\Candidate;
use App\Services\Interfaces\FileUploadServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CandidateCrudController extends AbstractCrudController
{
    public function __construct(private FileUploadServiceInterface $resumeUploadService)
    {
    }

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

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->handleResumeUpload($entityInstance);
    }

    private function handleResumeUpload(Candidate $candidate): void
    {

        if ($this->resumeUploadService->handleFileUpload($candidate, $this->getContext()))
        {
            $entityManager = $this->container->get('doctrine')->getManagerForClass(Candidate::class);
            $entityManager->persist($candidate);
            $entityManager->flush();
        };
    }

}
