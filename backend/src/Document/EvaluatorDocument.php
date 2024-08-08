<?php

namespace App\Document;

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "evaluators")]
class EvaluatorDocument extends UserDocument
{
    #[MongoDB\Field(type: "string")]
    private $specialization;

    #[MongoDB\ReferenceMany(targetDocument: InterviewDocument::class, mappedBy: "evaluators")]
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

    public function addInterview(InterviewDocument $interview): self
    {
        if (!$this->interviews->contains($interview)) {
            $this->interviews->add($interview);
            $interview->addEvaluator($this);
        }
        return $this;
    }

    public function removeInterview(InterviewDocument $interview): self
    {
        if ($this->interviews->removeElement($interview)) {
            $interview->removeEvaluator($this);
        }
        return $this;
    }
}
