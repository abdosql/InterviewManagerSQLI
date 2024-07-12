<?php

namespace App\Event\CandidateEvents;

use Symfony\Contracts\EventDispatcher\Event;

class CandidateCreatedEvent extends Event
{
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $phone;
    private string $email;
    private string $address;
    private ?\DateTimeInterface $hireDate;
    private string $resumeFilePath;

    /**
     * @param int $id
     * @param string $firstName
     * @param string $lastName
     * @param string $phone
     * @param string $email
     * @param string $address
     * @param string $resumeFilePath
     * @param \DateTimeInterface|null $hireDate
     */
    public function __construct(
        int $id,
        string $firstName,
        string $lastName,
        string $phone,
        string $email,
        string $address,
        string $resumeFilePath,
        ?\DateTimeInterface $hireDate = null
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;
        $this->email = $email;
        $this->address = $address;
        $this->hireDate = $hireDate;
        $this->resumeFilePath = $resumeFilePath;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getHireDate(): ?\DateTimeInterface
    {
        return $this->hireDate;
    }

    public function getResumeFilePath(): string
    {
        return $this->resumeFilePath;
    }
}
