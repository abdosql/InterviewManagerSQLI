<?php

namespace App\Services\Interfaces;

use App\Entity\Candidate;
use App\Entity\Resume;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileUploadServiceInterface
{
    public function handleFileUpload(Candidate $candidate,$context): bool;
}