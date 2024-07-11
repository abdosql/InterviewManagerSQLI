<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Candidate;
use App\Entity\Resume;
use App\Services\Interfaces\FileUploadServiceInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class ResumeUploadService implements FileUploadServiceInterface
{
    public function __construct(
        private string $projectDir,
        private string $uploadDir,
    ) {
    }

    public function handleFileUpload(Candidate $candidate, $context): bool
    {
        $file = $context->getRequest()->files->get('Candidate')['resume_filePath'];
        try {
            if (!$file instanceof UploadedFile) {
                throw new FileException('Invalid file uploaded');
            }
            $allowMineTypes = ["application/pdf", "application/docx", "application/text"];
            if (!in_array($file->getMimeType(), $allowMineTypes)) {
                throw new FileException('Invalid file type. Only PDF, DOCX, and TXT are allowed.');
            }

        }catch (FileException $e) {
            throw new FileException($e->getMessage());
        }
        $fileName = \uniqid() . '.' . $file->guessExtension();
        $fullUploadPath = $this->projectDir . '/' . $this->uploadDir;

        try {
            $file->move($fullUploadPath, $fileName);
        } catch (FileException $e) {
            throw new FileException($e->getMessage());
        }

        $filePath = '/' . $fileName;

        if (!$candidate->getResume()) {
            $resume = new Resume();
            $resume->setCandidate($candidate);
            $candidate->setResume($resume);
        }

        $candidate->getResume()->setFilePath($filePath);

        return true;
    }
}