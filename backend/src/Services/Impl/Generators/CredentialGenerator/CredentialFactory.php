<?php

namespace App\Services\Impl\Generators\CredentialGenerator;

use Symfony\Component\String\Slugger\SluggerInterface;

class CredentialFactory
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function getUsernameGenerator(): UsernameGenerator
    {
        return new UsernameGenerator($this->slugger);
    }

    public function getPasswordGenerator(): PasswordGenerator
    {
        return new PasswordGenerator();
    }
}