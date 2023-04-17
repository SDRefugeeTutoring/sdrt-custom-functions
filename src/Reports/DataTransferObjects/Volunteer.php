<?php

//declare(strict_types=1);

namespace SDRT\CustomFunctions\Reports\DataTransferObjects;

use DateTimeImmutable;
use DateTimeInterface;

class Volunteer
{
    public int $id;

    public string $firstName;

    public string $lastName;

    public string $email;

    public int $totalSessions;

    public int $totalK5;

    public int $totalMiddleHigh;

    public DateTimeInterface $firstSessionDate;

    public DateTimeInterface $latestSessionDate;

    public float $yearsActive;

    public static function fromArray(array $data): self
    {
        $volunteer = new self();
        $volunteer->id = $data['id'];
        $volunteer->firstName = $data['firstName'];
        $volunteer->lastName = $data['lastName'];
        $volunteer->email = $data['email'];
        $volunteer->totalSessions = (int)$data['totalSessions'];
        $volunteer->totalK5 = (int)$data['totalK5'];
        $volunteer->totalMiddleHigh = (int)$data['totalMiddleHigh'];
        $volunteer->firstSessionDate = new DateTimeImmutable($data['firstSessionDate']);
        $volunteer->latestSessionDate = new DateTimeImmutable($data['latestSessionDate']);
        $volunteer->yearsActive = (float)$data['yearsActive'];

        return $volunteer;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'totalSessions' => $this->totalSessions,
            'totalK5' => $this->totalK5,
            'totalMiddleHigh' => $this->totalMiddleHigh,
            'firstSessionDate' => $this->firstSessionDate->format('Y-m-d'),
            'latestSessionDate' => $this->latestSessionDate->format('Y-m-d'),
            'yearsActive' => $this->yearsActive,
        ];
    }
}