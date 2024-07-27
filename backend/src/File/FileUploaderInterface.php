<?php

namespace App\File;

use Symfony\Component\HttpFoundation\File\File;

interface FileUploaderInterface
{
    public function upload(File $file): string;
}