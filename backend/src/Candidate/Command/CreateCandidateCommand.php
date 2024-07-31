<?php

namespace App\Candidate\Command;

use App\Candidate\Command\Abstract\AbstractCommand;
use App\Entity\Candidate;
use App\Message\Candidate\CandidateCreatedMessage;
use App\Services\Impl\CandidateService;

readonly class CreateCandidateCommand extends AbstractCommand
{
    public function __construct(private Candidate $candidate, private CandidateService $candidateService)
    {
        parent::__construct($candidateService);
    }

    /**
     * @return int
     */
    public function execute(): int
    {
        $this->candidateService->saveEntity($this->candidate);
        return $this->candidate->getId();
    }
    public static function getMessageClass(): string
    {
        return CandidateCreatedMessage::class;
    }
}