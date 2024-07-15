<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "interviews")]
class InterviewDocument
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "date")]
    private $interviewDate;

    #[MongoDB\Field(type: "string")]
    private $interviewLocation;

    #[MongoDB\ReferenceOne(targetDocument: CandidateDocument::class, inversedBy: "interviews")]
    private $candidate;

    #[MongoDB\ReferenceOne(targetDocument: EvaluatorDocument::class, inversedBy: "interviews")]
    private $evaluator;

    #[MongoDB\ReferenceOne(targetDocument: HRManagerDocument::class, inversedBy: "interviews")]
    private $hrManager;

    #[MongoDB\ReferenceMany(targetDocument: AppreciationDocument::class, mappedBy: "interview")]
    private $appreciations;

    public function __construct()
    {
        $this->appreciations = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getInterviewDate(): ?\DateTimeInterface
    {
        return $this->interviewDate;
    }

    public function setInterviewDate(\DateTimeInterface $interviewDate): self
    {
        $this->interviewDate = $interviewDate;
        return $this;
    }

    public function getInterviewLocation(): ?string
    {
        return $this->interviewLocation;
    }

    public function setInterviewLocation(string $interviewLocation): self
    {
        $this->interviewLocation = $interviewLocation;
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

    public function getEvaluator(): ?EvaluatorDocument
    {
        return $this->evaluator;
    }

    public function setEvaluator(?EvaluatorDocument $evaluator): self
    {
        $this->evaluator = $evaluator;
        return $this;
    }

    public function getHrManager(): ?HRManagerDocument
    {
        return $this->hrManager;
    }

    public function setHrManager(?HRManagerDocument $hrManager): self
    {
        $this->hrManager = $hrManager;
        return $this;
    }

    public function getAppreciations(): Collection
    {
        return $this->appreciations;
    }

    public function addAppreciation(AppreciationDocument $appreciation): self
    {
        if (!$this->appreciations->contains($appreciation)) {
            $this->appreciations[] = $appreciation;
            $appreciation->setInterview($this);
        }
        return $this;
    }

    public function removeAppreciation(AppreciationDocument $appreciation): self
    {
        if ($this->appreciations->removeElement($appreciation)) {
            if ($appreciation->getInterview() === $this) {
                $appreciation->setInterview(null);
            }
        }
        return $this;
    }
}