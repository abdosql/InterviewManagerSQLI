<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "candidate_phases")]
class CandidatePhase
{
    #[MongoDB\Id]
    private ?string $id;

    #[MongoDB\Field(type: "string")]
    private ?string $phase;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeInterface $startDate;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeInterface $endDate;

    #[MongoDB\Field(type: "string")]
    private ?string $result;

    #[MongoDB\ReferenceOne(targetDocument: Candidate::class, inversedBy: "candidatePhases")]
    private ?Candidate $candidate;

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

    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    public function setCandidate(?Candidate $candidate): self
    {
        $this->candidate = $candidate;
        return $this;
    }
}