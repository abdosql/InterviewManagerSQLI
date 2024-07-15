<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "interview_statuses")]
class InterviewStatus
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    private $status;

    #[MongoDB\Field(type: "date")]
    private $statusDate;

    #[MongoDB\ReferenceOne(targetDocument: Interview::class, inversedBy: "status")]
    private $interview;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatusDate(): ?\DateTimeInterface
    {
        return $this->statusDate;
    }

    public function setStatusDate(\DateTimeInterface $statusDate): self
    {
        $this->statusDate = $statusDate;
        return $this;
    }

    public function getInterview(): ?Interview
    {
        return $this->interview;
    }

    public function setInterview(Interview $interview): self
    {
        $this->interview = $interview;
        return $this;
    }
}