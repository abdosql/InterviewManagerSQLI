<?php

namespace App\Entity;

use App\Repository\EntityRepository\CandidateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity(repositoryClass: CandidateRepository::class)]
class Candidate extends Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 255)]
    private ?string $address = null;
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $hire_date = null;

    /**
     * @var Collection<int, Interview>
     */
    #[ORM\OneToMany(targetEntity: Interview::class, mappedBy: 'candidate')]
    private Collection $interviews;

    /**
     * @var Collection<int, CandidatePhase>
     */
    #[ORM\OneToMany(targetEntity: CandidatePhase::class, mappedBy: 'candidate')]
    private Collection $candidatePhases;

    #[ORM\OneToOne(inversedBy: 'candidate', cascade: ['persist', 'remove'])]
    private ?Resume $resume = null;

    public function __construct()
    {
        $this->interviews = new ArrayCollection();
        $this->candidatePhases = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getHireDate(): ?\DateTimeInterface
    {
        return $this->hire_date;
    }

    public function setHireDate(?\DateTimeInterface $hire_date): static
    {
        $this->hire_date = $hire_date;

        return $this;
    }

    /**
     * @return Collection<int, Interview>
     */
    public function getInterviews(): Collection
    {
        return $this->interviews;
    }

    public function addInterview(Interview $interview): static
    {
        if (!$this->interviews->contains($interview)) {
            $this->interviews->add($interview);
            $interview->setCandidate($this);
        }

        return $this;
    }

    public function removeInterview(Interview $interview): static
    {
        if ($this->interviews->removeElement($interview)) {
            // set the owning side to null (unless already changed)
            if ($interview->getCandidate() === $this) {
                $interview->setCandidate(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CandidatePhase>
     */
    public function getCandidatePhases(): Collection
    {
        return $this->candidatePhases;
    }

    public function addCandidatePhase(CandidatePhase $candidatePhase): static
    {
        if (!$this->candidatePhases->contains($candidatePhase)) {
            $this->candidatePhases->add($candidatePhase);
            $candidatePhase->setCandidate($this);
        }

        return $this;
    }

    public function removeCandidatePhase(CandidatePhase $candidatePhase): static
    {
        if ($this->candidatePhases->removeElement($candidatePhase)) {
            // set the owning side to null (unless already changed)
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

    public function setResume(?Resume $resume): static
    {
        $this->resume = $resume;

        return $this;
    }
}
