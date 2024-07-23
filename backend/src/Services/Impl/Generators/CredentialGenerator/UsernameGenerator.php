<?php
namespace App\Services\Impl\Generators\CredentialGenerator;

use App\Services\CredentialGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UsernameGenerator implements CredentialGeneratorInterface
{
    private SluggerInterface $slugger;
    public function __construct(SluggerInterface $slugger){
        $this->slugger = $slugger;
    }
    /**
     * @return string
     */
    public function generate(): string
    {
        return $this->slugger->slug("user-". time())->lower();
    }
}