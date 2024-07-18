<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */
declare(strict_types=1);

namespace App\Services\Impl;

use App\Services\FileUploadServiceInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class ResumeUploadService implements FileUploadServiceInterface
{
    public function __construct(
        private string $projectDir,
        private string $uploadDir,
    )
    {}

    public function handleFileUpload($file): string
    {
        try {
            if (!$file instanceof UploadedFile) {
                throw new FileException('Invalid file uploaded');
            }
            $allowMineTypes = ["application/pdf", "application/docx", "application/text"];
            if (!in_array($file->getMimeType(), $allowMineTypes)) {
                throw new FileException('Invalid file type. Only PDF, DOCX, and TXT are allowed.');
            };
            $fileName = \uniqid() . '.' . $file->guessExtension();
            $fullUploadPath = $this->projectDir . '/' . $this->uploadDir;
            $file->move($fullUploadPath, $fileName);
            return '/' . $fileName;
        }catch (FileException $e) {
            throw new FileException($e->getMessage());
        }

    }
}