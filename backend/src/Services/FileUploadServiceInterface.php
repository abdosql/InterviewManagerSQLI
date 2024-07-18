<?php

namespace App\Services;

interface FileUploadServiceInterface
{
    public function handleFileUpload($file): string;
}