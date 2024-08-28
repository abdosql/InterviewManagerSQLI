<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "appreciations")]
class AppreciationDocument
{
    #[MongoDB\Id]
    private ?string $id;

    #[MongoDB\Field(type: "string")]
    private ?string $comment;

    #[MongoDB\Field(type: "int")]
    private ?int $score;

    #[MongoDB\ReferenceOne(targetDocument: InterviewDocument::class, inversedBy: "appreciations")]
    private ?InterviewDocument $interview;

    #[MongoDB\Field(type: "int")]
    protected ?int $entityId;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;
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
    public function getEntityId():?int
    {
        return $this->entityId;
    }

    /**
     * @param int|null $entityId
     * @return AppreciationDocument
     */
    public function setEntityId(?int $entityId): self
    {
        $this->entityId = $entityId;
        return $this;
    }

}