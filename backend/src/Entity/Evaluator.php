<?php

namespace App\Entity;

use App\Repository\EntityRepository\EvaluatorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvaluatorRepository::class)]
class Evaluator extends User
{
    #[ORM\Column(length: 255)]
    private ?string $specialization = null;

    /**
     * @var Collection<int, Interview>
     */
    #[ORM\OneToMany(targetEntity: Interview::class, mappedBy: 'evaluator')]
    private Collection $interviews;

    public function __construct()
    {
        parent::__construct();
        $this->interviews = new ArrayCollection();
    }

    public function getSpecialization(): ?string
    {
        return $this->specialization;
    }

    public function setSpecialization(string $specialization): static
    {
        $this->specialization = $specialization;

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
            $interview->setEvaluator($this);
        }

        return $this;
    }

    public function removeInterview(Interview $interview): static
    {
        if ($this->interviews->removeElement($interview)) {
            // set the owning side to null (unless already changed)
            if ($interview->getEvaluator() === $this) {
                $interview->setEvaluator(null);
            }
        }

        return $this;
    }
}
