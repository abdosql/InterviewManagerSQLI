<?php
declare(strict_types=1);
namespace App\Services\Manager;

use App\Entity\User;
use App\Services\Impl\Generators\CredentialGenerator\CredentialFactory;
use App\Services\Impl\Generators\CredentialGenerator\PasswordGenerator;
use App\Services\Impl\Generators\CredentialGenerator\UsernameGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCredentialManager
{
    private UsernameGenerator $usernameGenerator;
    private PasswordGenerator $passwordGenerator;


    public function __construct(
        private CredentialFactory $credentialFactory,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->usernameGenerator = $this->credentialFactory->getUsernameGenerator();
        $this->passwordGenerator = $this->credentialFactory->getPasswordGenerator();
    }

    public function generateCredentials(User $user): array
    {
        $plainPassword = $this->usernameGenerator->generate();
        return [
            "username" => $this->usernameGenerator->generate(),
            "password" => $plainPassword,
            "hashedPassword" => $this->passwordHasher->hashPassword($user, $plainPassword)
        ];
    }

    public function applyCredentialsToUser(User &$user, array $credentials): void
    {
        $user->setUsername($credentials['username']);
        $user->setPassword($credentials['hashedPassword']);
    }
}