<?php

namespace App\Command\Candidate;

use App\Command\Abstract\AbstractCommand;
use App\Entity\Candidate;
use App\Message\Candidate\CandidateUpdatedMessage;
use App\Services\Impl\CandidateService;

readonly class UpdateCandidateCommand extends AbstractCommand
{
    public function __construct(private Candidate $candidate, private CandidateService $candidateService)
    {
        parent::__construct($candidateService);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function execute(): int
    {
        if ($this->candidate->getId() == null) {
            throw new \Exception('Candidate ID is required');
        }
        $this->candidateService->updateEntity($this->candidate);
        return $this->candidate->getId();
    }

    public static function getMessageClass(): string
    {
        return CandidateUpdatedMessage::class;
    }
}
