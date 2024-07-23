<?php

namespace App\Command\UserCommands;

use App\Command\CommandInterface;
use App\Entity\Evaluator;
use App\Services\Impl\EvaluatorService;
use App\Services\Manager\UserCredentialManager;

class CreateUserCommand implements CommandInterface
{
    private Evaluator $evaluator;
    private EvaluatorService $evaluatorService;
    private UserCredentialManager $credentialManager;
    public function __construct(Evaluator $evaluator, EvaluatorService $evaluatorService, UserCredentialManager $credentialManager)
    {
        $this->evaluator = $evaluator;
        $this->evaluatorService = $evaluatorService;
        $this->credentialManager = $credentialManager;
    }

    public function execute(): object
    {
        $credentialManager = $this->credentialManager->generateCredentials($this->evaluator);
        $this->credentialManager->applyCredentialsToUser($this->evaluator, $credentialManager);
        $this->evaluatorService->saveEntity($this->evaluator);
        return $this->evaluator;
    }
}