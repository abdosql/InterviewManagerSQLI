<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "hrManagers")]
class HRManagerDocument extends UserDocument
{
    #[MongoDB\Field(type: "string")]
    private $department;

    #[MongoDB\Field(type: "string")]
    private $position;

    #[MongoDB\ReferenceMany(targetDocument: InterviewDocument::class, mappedBy: "hrManager")]
    private $interviews;

    public function __construct()
    {
        parent::__construct();
        $this->interviews = new ArrayCollection();
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(string $department): self
    {
        $this->department = $department;
        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;
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
            $interview->setHrManager($this);
        }
        return $this;
    }

    public function removeInterview(InterviewDocument $interview): self
    {
        if ($this->interviews->removeElement($interview)) {
            if ($interview->getHrManager() === $this) {
                $interview->setHrManager(null);
            }
        }
        return $this;
    }
}