<?php

namespace App\Entity;

use App\Repository\EntityRepository\InterviewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterviewRepository::class)]
class Interview
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $interview_date = null;

    #[ORM\Column(length: 255)]
    private ?string $interview_location = null;

    #[ORM\ManyToOne(inversedBy: 'interviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Candidate $candidate = null;

    #[ORM\ManyToOne(inversedBy: 'interviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Evaluator $evaluator = null;

    #[ORM\ManyToOne(inversedBy: 'interviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?HRManager $hr_manager = null;

    /**
     * @var Collection<int, Appreciation>
     */
    #[ORM\OneToMany(targetEntity: Appreciation::class, mappedBy: 'interview')]
    private Collection $appreciations;

    public function __construct()
    {
        $this->appreciations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInterviewDate(): ?\DateTimeInterface
    {
        return $this->interview_date;
    }

    public function setInterviewDate(\DateTimeInterface $interview_date): static
    {
        $this->interview_date = $interview_date;

        return $this;
    }

    public function getInterviewLocation(): ?string
    {
        return $this->interview_location;
    }

    public function setInterviewLocation(string $interview_location): static
    {
        $this->interview_location = $interview_location;

        return $this;
    }

    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    public function setCandidate(?Candidate $candidate): static
    {
        $this->candidate = $candidate;

        return $this;
    }

    public function getEvaluator(): ?Evaluator
    {
        return $this->evaluator;
    }

    public function setEvaluator(?Evaluator $evaluator): static
    {
        $this->evaluator = $evaluator;

        return $this;
    }

    public function getHrManager(): ?HRManager
    {
        return $this->hr_manager;
    }

    public function setHrManager(?HRManager $hr_manager): static
    {
        $this->hr_manager = $hr_manager;

        return $this;
    }

    /**
     * @return Collection<int, Appreciation>
     */
    public function getAppreciations(): Collection
    {
        return $this->appreciations;
    }

    public function addAppreciation(Appreciation $appreciation): static
    {
        if (!$this->appreciations->contains($appreciation)) {
            $this->appreciations->add($appreciation);
            $appreciation->setInterview($this);
        }

        return $this;
    }

    public function removeAppreciation(Appreciation $appreciation): static
    {
        if ($this->appreciations->removeElement($appreciation)) {
            // set the owning side to null (unless already changed)
            if ($appreciation->getInterview() === $this) {
                $appreciation->setInterview(null);
            }
        }

        return $this;
    }
}
