<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "notifications")]
class Notification
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    private $content;

    #[MongoDB\Field(type: "date")]
    private $notificationDate;

    #[MongoDB\ReferenceOne(targetDocument: User::class, inversedBy: "notifications")]
    private $user;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getNotificationDate(): ?\DateTimeInterface
    {
        return $this->notificationDate;
    }

    public function setNotificationDate(\DateTimeInterface $notificationDate): self
    {
        $this->notificationDate = $notificationDate;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
}