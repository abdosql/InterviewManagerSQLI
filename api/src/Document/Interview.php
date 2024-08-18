<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "interviews")]
class Interview
{
    #[MongoDB\Id]
    private ?string $id;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeInterface $interviewDate;

    #[MongoDB\Field(type: "string")]
    private ?string $interviewLocation;

    #[MongoDB\ReferenceOne(targetDocument: Candidate::class, inversedBy: "interviews")]
    private ?Candidate $candidate;

    #[MongoDB\ReferenceMany(targetDocument: Evaluator::class, inversedBy: "interviews")]
    private ArrayCollection $evaluators;

    #[MongoDB\ReferenceOne(targetDocument: HRManager::class, inversedBy: "interviews")]
    private ?HRManager $hrManager;

    #[MongoDB\ReferenceMany(targetDocument: Appreciation::class, mappedBy: "interview")]
    private ArrayCollection $appreciations;

//    #[MongoDB\Field(type: "int")]
//    protected ?int $entityId;

    public function __construct()
    {
        $this->appreciations = new ArrayCollection();
        $this->evaluators = new ArrayCollection();
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

    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    public function setCandidate(?Candidate $candidate): self
    {
        $this->candidate = $candidate;
        return $this;
    }

    public function getEvaluators(): Collection
    {
        return $this->evaluators;
    }

    public function addEvaluator(Evaluator $evaluator): self
    {
        if (!$this->evaluators->contains($evaluator)) {
            $this->evaluators->add($evaluator);
            $evaluator->addInterview($this);
        }
        return $this;
    }

    public function removeEvaluator(Evaluator $evaluator): self
    {
        if ($this->evaluators->removeElement($evaluator)) {
            $evaluator->removeInterview($this);
        }
        return $this;
    }

    public function getHrManager(): ?HRManager
    {
        return $this->hrManager;
    }

    public function setHrManager(?HRManager $hrManager): self
    {
        $this->hrManager = $hrManager;
        return $this;
    }

    public function getAppreciations(): Collection
    {
        return $this->appreciations;
    }

    public function addAppreciation(Appreciation $appreciation): self
    {
        if (!$this->appreciations->contains($appreciation)) {
            $this->appreciations[] = $appreciation;
            $appreciation->setInterview($this);
        }
        return $this;
    }

    public function removeAppreciation(Appreciation $appreciation): self
    {
        if ($this->appreciations->removeElement($appreciation)) {
            if ($appreciation->getInterview() === $this) {
                $appreciation->setInterview(null);
            }
        }
        return $this;
    }
//    public function getEntityId():?int
//    {
//        return $this->entityId;
//    }
//    public function setEntityId(?int $entityId): self
//    {
//        $this->entityId = $entityId;
//        return $this;
//    }
}
