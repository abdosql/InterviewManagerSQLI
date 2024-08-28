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
    private ?\DateTimeInterface $interviewDate = null;

    #[ORM\Column(length: 255)]
    private ?string $interviewLocation = null;

    #[ORM\ManyToOne(inversedBy: 'interviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Candidate $candidate = null;

    #[ORM\ManyToOne(inversedBy: 'interviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?HRManager $hrManager = null;

    /**
     * @var Collection<int, Appreciation>
     */
    #[ORM\OneToMany(targetEntity: Appreciation::class, mappedBy: 'interview')]
    private Collection $appreciations;

    /**
     * @var Collection<int, Evaluator>
     */
    #[ORM\ManyToMany(targetEntity: Evaluator::class, mappedBy: 'interviews')]
    private Collection $evaluators;

    /**
     * @var Collection<int, InterviewStatus>
     */
    #[ORM\OneToMany(targetEntity: InterviewStatus::class, mappedBy: 'interview', cascade: ['persist'])]
    private Collection $interviewStatuses;


    public function __construct()
    {
        $this->appreciations = new ArrayCollection();
        $this->evaluators = new ArrayCollection();
        $this->interviewStatuses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInterviewDate(): ?\DateTimeInterface
    {
        return $this->interviewDate;
    }

    public function setInterviewDate(\DateTimeInterface $interviewDate): static
    {
        $this->interviewDate = $interviewDate;

        return $this;
    }

    public function getInterviewLocation(): ?string
    {
        return $this->interviewLocation;
    }

    public function setInterviewLocation(string $interviewLocation): static
    {
        $this->interviewLocation = $interviewLocation;

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

    public function getHrManager(): ?HRManager
    {
        return $this->hrManager;
    }

    public function setHrManager(?HRManager $hrManager): static
    {
        $this->hrManager = $hrManager;

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

    /**
     * @return Collection<int, Evaluator>
     */
    public function getEvaluators(): Collection
    {
        return $this->evaluators;
    }

    public function addEvaluator(Evaluator $evaluator): static
    {
        if (!$this->evaluators->contains($evaluator)) {
            $this->evaluators->add($evaluator);
            $evaluator->addInterview($this);
        }

        return $this;
    }

    public function removeEvaluator(Evaluator $evaluator): static
    {
        if ($this->evaluators->removeElement($evaluator)) {
            $evaluator->removeInterview($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, InterviewStatus>
     */
    public function getInterviewStatuses(): Collection
    {
        return $this->interviewStatuses;
    }

    public function addInterviewStatus(InterviewStatus $interviewStatus): static
    {
        if (!$this->interviewStatuses->contains($interviewStatus)) {
            $this->interviewStatuses->add($interviewStatus);
            $interviewStatus->setInterview($this);
        }

        return $this;
    }

    public function removeInterviewStatus(InterviewStatus $interviewStatus): static
    {
        if ($this->interviewStatuses->removeElement($interviewStatus)) {
            // set the owning side to null (unless already changed)
            if ($interviewStatus->getInterview() === $this) {
                $interviewStatus->setInterview(null);
            }
        }

        return $this;
    }

}
