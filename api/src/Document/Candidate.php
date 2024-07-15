<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[MongoDB\Document(collection: "candidates")]
class Candidate
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    private ?string $address;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeInterface $hireDate;

    #[MongoDB\ReferenceMany(targetDocument: Interview::class, mappedBy: "candidate")]
    private ArrayCollection $interviews;

    #[MongoDB\ReferenceMany(targetDocument: CandidatePhase::class, mappedBy: "candidate")]
    private ArrayCollection $candidatePhases;

    #[MongoDB\ReferenceOne(targetDocument: Resume::class)]
    private ?Resume $resume;

    public function __construct()
    {
        $this->interviews = new ArrayCollection();
        $this->candidatePhases = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getHireDate(): ?\DateTimeInterface
    {
        return $this->hireDate;
    }

    public function setHireDate(\DateTimeInterface $hireDate): self
    {
        $this->hireDate = $hireDate;
        return $this;
    }

    public function getInterviews(): Collection
    {
        return $this->interviews;
    }

    public function addInterview(Interview $interview): self
    {
        if (!$this->interviews->contains($interview)) {
            $this->interviews->add($interview);
            $interview->setCandidate($this);
        }
        return $this;
    }

    public function removeInterview(Interview $interview): self
    {
        if ($this->interviews->removeElement($interview)) {
            if ($interview->getCandidate() === $this) {
                $interview->setCandidate(null);
            }
        }
        return $this;
    }

    public function getCandidatePhases(): Collection
    {
        return $this->candidatePhases;
    }

    public function addCandidatePhase(CandidatePhase $candidatePhase): self
    {
        if (!$this->candidatePhases->contains($candidatePhase)) {
            $this->candidatePhases->add($candidatePhase);
            $candidatePhase->setCandidate($this);
        }
        return $this;
    }

    public function removeCandidatePhase(CandidatePhase $candidatePhase): self
    {
        if ($this->candidatePhases->removeElement($candidatePhase)) {
            if ($candidatePhase->getCandidate() === $this) {
                $candidatePhase->setCandidate(null);
            }
        }
        return $this;
    }

    public function getResume(): ?Resume
    {
        return $this->resume;
    }

    public function setResume(?Resume $resume): self
    {
        $this->resume = $resume;
        return $this;
    }
}