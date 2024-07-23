<?php

namespace App\Services;

interface CredentialGeneratorInterface
{
    public function generate(): string;
}