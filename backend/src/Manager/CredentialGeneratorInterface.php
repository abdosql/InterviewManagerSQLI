<?php

namespace App\Manager;

interface CredentialGeneratorInterface
{
    public function generate(): string;
}