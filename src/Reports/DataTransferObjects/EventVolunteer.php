<?php

namespace SDRT\CustomFunctions\Reports\DataTransferObjects;

class EventVolunteer
{
    public int $id;

    public string $name;


    public string $rsvp;

    public string $attended;

    public static function fromArray(array $data): self
    {
        $eventVolunteer = new self();

        $eventVolunteer->id = (int)$data['id'];
        $eventVolunteer->name = $data['name'];
        $eventVolunteer->rsvp = $data['rsvp'];
        $eventVolunteer->attended = $data['attended'];

        return $eventVolunteer;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => html_entity_decode($this->name),
            'rsvp' => $this->rsvp,
            'attended' => $this->attended,
        ];
    }
}