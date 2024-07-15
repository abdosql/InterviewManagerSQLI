<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "resumes")]
class ResumeDocument
{
    #[MongoDB\Id]
    private ?string $id;

    #[MongoDB\Field(type: "string")]
    private ?string $filePath;

    #[MongoDB\ReferenceOne(targetDocument: CandidateDocument::class, inversedBy: "resume")]
    private ?CandidateDocument $candidate;

    #[MongoDB\Field(type: "int")]
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

    public function getCandidate(): ?CandidateDocument
    {
        return $this->candidate;
    }

    public function setCandidate(?CandidateDocument $candidate): self
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
     * @return ResumeDocument
     */
    public function setEntityId(?int $entityId): self
    {
        $this->entityId = $entityId;
        return $this;
    }
}