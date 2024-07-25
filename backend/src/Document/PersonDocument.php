<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\EmbeddedDocument]
abstract class PersonDocument
{
    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "First name should not be blank")]
    protected ?string $firstName;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "Last name should not be blank")]
    protected ?string $lastName;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank(message: "Phone number should not be blank")]
    protected ?string $phone;

    #[MongoDB\Field(type: "string")]
    #[MongoDB\Index(unique: true)]
    #[Assert\NotBlank(message: 'Email should not be blank')]
    #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
    protected ?string $email = null;

    #[MongoDB\Field(type: "int")]
    protected ?int $entityId;

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function getEntityId():?int
    {
        return $this->entityId;
    }

    /**
     * @param int|null $entityId
     * @return PersonDocument
     */
    public function setEntityId(?int $entityId): self
    {
        $this->entityId = $entityId;
        return $this;
    }

}