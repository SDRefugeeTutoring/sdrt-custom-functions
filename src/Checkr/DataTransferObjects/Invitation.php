<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Checkr\DataTransferObjects;

class Invitation
{
    public string $id;
    public string $candidateId;
    public string $invitationUrl;
    public string $status;

    public static function fromResponse(array $response)
    {
        $invitation = new self();

        $invitation->id = $response['id'];
        $invitation->candidateId = $response['candidate_id'];
        $invitation->invitationUrl = $response['invitation_url'];
        $invitation->status = $response['status'];

        return $invitation;
    }
}