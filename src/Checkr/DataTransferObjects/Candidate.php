<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Checkr\DataTransferObjects;

class Candidate
{
    public string $id;
    public string $firstName;
    public string $lastName;
    public string $email;

    public static function fromResponse(array $response)
    {
        $candidate = new self();

        $candidate->id = $response['id'];
        $candidate->firstName = $response['first_name'];
        $candidate->lastName = $response['last_name'];
        $candidate->email = $response['email'];

        return $candidate;
    }
}