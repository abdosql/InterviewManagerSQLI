<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "interviewStatuses")]
class InterviewStatusDocument
{
    #[MongoDB\Id]
    private ?string $id;

    #[MongoDB\Field(type: "string")]
    private ?string $status;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeInterface $statusDate;

    #[MongoDB\ReferenceOne(targetDocument: InterviewDocument::class, cascade: ["persist"], inversedBy: "interviewStatuses")]
    private ?InterviewDocument $interview = null;

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

    public function getInterview(): ?InterviewDocument
    {
        return $this->interview;
    }

    public function setInterview(?InterviewDocument $interview): self
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