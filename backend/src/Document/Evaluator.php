<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "users")]
class Evaluator extends User
{
    #[MongoDB\Field(type: "string")]
    private $specialization;

    #[MongoDB\ReferenceMany(targetDocument: Interview::class, mappedBy: "evaluator")]
    private $interviews;

    public function __construct()
    {
        parent::__construct();
        $this->interviews = new ArrayCollection();
    }

    public function getSpecialization(): ?string
    {
        return $this->specialization;
    }

    public function setSpecialization(string $specialization): self
    {
        $this->specialization = $specialization;
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
            $interview->setEvaluator($this);
        }
        return $this;
    }

    public function removeInterview(Interview $interview): self
    {
        if ($this->interviews->removeElement($interview)) {
            if ($interview->getEvaluator() === $this) {
                $interview->setEvaluator(null);
            }
        }
        return $this;
    }
}