<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use App\Candidate\Provider\Data\CandidateDataProvider;
use App\Repository\CandidateRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiResource(provider: CandidateDataProvider::class)]
#[MongoDB\Document(collection: "candidates", repositoryClass: CandidateRepository::class)]
class Candidate extends Person
{
    #[MongoDB\Id]
    private ?string $id;

    #[MongoDB\Field(type: "string")]
    private ?string $address;

    #[MongoDB\Field(type: "date", nullable: true)]
    private ?\DateTimeInterface $hireDate = null;

    #[MongoDB\ReferenceMany(targetDocument: Interview::class, mappedBy: "candidate")]
    private ArrayCollection $interviews;

    #[MongoDB\ReferenceMany(targetDocument: CandidatePhase::class, mappedBy: "candidate")]
    private ArrayCollection $candidatePhases;

    #[MongoDB\ReferenceOne(targetDocument: Resume::class, cascade: ["persist", "delete"])]
    private ?Resume $resume;

    public function __construct()
    {
        $this->interviews = new ArrayCollection();
        $this->candidatePhases = new ArrayCollection();
    }

    public function getId(): ?string
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

    public function setHireDate(?\DateTimeInterface $hireDate): self
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
        if ($this->resume === null) {
            $this->resume = new Resume();
        }
        return $this->resume;
    }

    public function setResume(?Resume $resume): self
    {
        $this->resume = $resume;
        return $this;
    }


}