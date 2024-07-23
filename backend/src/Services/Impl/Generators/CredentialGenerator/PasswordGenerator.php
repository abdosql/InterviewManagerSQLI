<?php

namespace App\Services\Impl\Generators\CredentialGenerator;

use App\Services\CredentialGeneratorInterface;

class PasswordGenerator implements CredentialGeneratorInterface
{

    public function generate(): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $length = 8;
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $password;
    }
}