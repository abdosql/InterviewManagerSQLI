<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "candidate_phases")]
class CandidatePhaseDocument
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    private $phase;

    #[MongoDB\Field(type: "date")]
    private $startDate;

    #[MongoDB\Field(type: "date")]
    private $endDate;

    #[MongoDB\Field(type: "string")]
    private $result;

    #[MongoDB\ReferenceOne(targetDocument: CandidateDocument::class, inversedBy: "candidatePhases")]
    private $candidate;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPhase(): ?string
    {
        return $this->phase;
    }

    public function setPhase(string $phase): self
    {
        $this->phase = $phase;
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $result): self
    {
        $this->result = $result;
        return $this;
    }

    public function getCandidate(): ?CandidateDocument
    {
        return $this->candidate;
    }

    public function setCandidate(?CandidateDocument $candidate): self
    {
        $this->candidate = $candidate;
        return $this;
    }
}