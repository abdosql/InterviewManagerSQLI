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
    private ?\DateTimeInterface $interviewDate;

    #[MongoDB\Field(type: "string")]
    private ?string $interviewLocation;

    #[MongoDB\ReferenceOne(targetDocument: CandidateDocument::class, inversedBy: "interviews")]
    private ?CandidateDocument $candidate;

    #[MongoDB\ReferenceMany(targetDocument: EvaluatorDocument::class, inversedBy: "interviews")]
    private Collection $evaluators;

    #[MongoDB\ReferenceOne(targetDocument: HRManagerDocument::class, inversedBy: "interviews")]
    private ?HRManagerDocument $hrManager;

    #[MongoDB\ReferenceMany(targetDocument: AppreciationDocument::class, cascade: ["persist", "remove"], mappedBy: "interview")]
    private Collection $appreciations;

    #[MongoDB\ReferenceMany(targetDocument: InterviewStatusDocument::class, cascade: ["persist", "remove"], mappedBy: 'interview')]
    private Collection $interviewStatuses;

    #[MongoDB\Field(type: "int")]
    private ?int $entityId;

    public function __construct()
    {
        $this->appreciations = new ArrayCollection();
        $this->evaluators = new ArrayCollection();
        $this->interviewStatuses = new ArrayCollection();
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

    public function getEvaluators(): Collection
    {
        return $this->evaluators;
    }

    public function addEvaluator(EvaluatorDocument $evaluator): self
    {
        if (!$this->evaluators->contains($evaluator)) {
            $this->evaluators->add($evaluator);
            $evaluator->addInterview($this);
        }
        return $this;
    }

    public function removeEvaluator(EvaluatorDocument $evaluator): self
    {
        if ($this->evaluators->removeElement($evaluator)) {
            $evaluator->removeInterview($this);
        }
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

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(?int $entityId): self
    {
        $this->entityId = $entityId;
        return $this;
    }

    public function getInterviewStatuses(): Collection
    {
        return $this->interviewStatuses;
    }

    public function addInterviewStatus(InterviewStatusDocument $interviewStatus): self
    {
        if (!$this->interviewStatuses->contains($interviewStatus)) {
            $this->interviewStatuses[] = $interviewStatus;
            $interviewStatus->setInterview($this);
        }
        return $this;
    }

    public function removeInterviewStatus(InterviewStatusDocument $interviewStatus): self
    {
        if ($this->interviewStatuses->removeElement($interviewStatus)) {
            if ($interviewStatus->getInterview() === $this) {
                $interviewStatus->setInterview(null);
            }
        }
        return $this;
    }
}