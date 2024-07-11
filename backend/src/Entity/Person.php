<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class Person
{
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'First name should not be blank')]
    protected ?string $first_name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Last name should not be blank')]
    protected ?string $last_name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Phone number should not be blank')]
    protected ?string $phone = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Email should not be blank')]
    #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
    protected ?string $email = null;

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
