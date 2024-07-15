<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "resumes")]
class Resume
{
    #[MongoDB\Id]
    private ?string $id;

    #[MongoDB\Field(type: "string")]
    private ?string $filePath;

    #[MongoDB\ReferenceOne(targetDocument: Candidate::class, mappedBy: "resume")]
    private ?Candidate $candidate;

    #[MongoDB\Field(type: "Integer")]
    protected ?int $entityId;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    public function setCandidate(?Candidate $candidate): self
    {
        $this->candidate = $candidate;
        return $this;
    }
    public function getEntityId():?int
    {
        return $this->entityId;
    }

    /**
     * @param int|null $entityId
     * @return Resume
     */
    public function setEntityId(?int $entityId): self
    {
        $this->entityId = $entityId;
        return $this;
    }
}