<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Candidate;
use App\Entity\Resume;
use App\Services\Interfaces\FileUploadServiceInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final readonly class ResumeUploadService implements FileUploadServiceInterface
{
    public function __construct(
        private string $projectDir,
        private string $uploadDir,
    ) {
    }

    public function handleFileUpload(Candidate $candidate, $file): bool
    {
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