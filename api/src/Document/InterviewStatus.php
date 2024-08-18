<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "interview_statuses")]
class InterviewStatus
{
    #[MongoDB\Id]
    private ?string $id;

    #[MongoDB\Field(type: "string")]
    private ?string $status;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeInterface $statusDate;

    #[MongoDB\ReferenceOne(targetDocument: Interview::class, inversedBy: "status")]
    private ?Interview $interview;

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