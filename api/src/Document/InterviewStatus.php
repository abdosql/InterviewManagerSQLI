<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "interviewStatuses")]
class InterviewStatus
{
    #[MongoDB\Id]
    private ?string $id;

    #[MongoDB\Field(type: "string")]
    private ?string $status;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeInterface $statusDate;

    #[MongoDB\ReferenceOne(targetDocument: Interview::class, cascade: ["persist"], inversedBy: "interviewStatuses")]
    private ?Interview $interview = null;
    #[MongoDB\Field(type: "int")]
    private ?int $entityId;

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

    public function setInterview(?Interview $interview): self
    {
        $this->interview = $interview;
        return $this;
    }
    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(?int $entityId): self
    {
        $this->entityId = $entityId;
        return $this;
    }
}