<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "agenda")]
class Agenda
{
    #[MongoDB\Id]
    private ?string $id;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeInterface $date;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeInterface $startTime;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeInterface $endTime;

    #[MongoDB\Field(type: "string")]
    private ?string $status;

    #[MongoDB\Field(type: "string")]
    private ?string $description;

    #[MongoDB\ReferenceOne(targetDocument: Interview::class)]
    private ?Interview $interview;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
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