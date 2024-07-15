<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "appreciations")]
class AppreciationDocument
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    private $comment;

    #[MongoDB\Field(type: "int")]
    private $score;

    #[MongoDB\ReferenceOne(targetDocument: InterviewDocument::class, inversedBy: "appreciations")]
    private $interview;

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
}