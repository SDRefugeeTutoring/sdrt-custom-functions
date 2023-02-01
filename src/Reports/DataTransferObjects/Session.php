<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Reports\DataTransferObjects;

use DateTime;

class Session
{
    public int $id;
    public string $name;
    public string $category;
    public int $totalAttending;
    public int $totalAttended;
    public DateTime $startDate;
    public DateTime $endDate;

    public static function fromArray(array $data): self
    {
        $session = new self();

        $session->id = (int)$data['id'];
        $session->name = $data['name'];
        $session->category = $data['category'];
        $session->totalAttending = (int)$data['totalAttending'];
        $session->totalAttended = (int)$data['totalAttended'];
        $session->startDate = new DateTime($data['startDate']);
        $session->endDate = new DateTime($data['endDate']);

        return $session;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => html_entity_decode($this->name),
            'category' => $this->category,
            'totalAttending' => $this->totalAttending,
            'totalAttended' => $this->totalAttended,
            'startDate' => $this->startDate->format('Y-m-d H:i:s'),
            'endDate' => $this->endDate->format('Y-m-d H:i:s'),
        ];
    }
}