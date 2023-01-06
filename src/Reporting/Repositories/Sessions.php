<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Reporting\Repositories;

use DateTimeInterface;

class Sessions
{
    /**
     * Retrieves the following statistics for all sessions:
     * - Total number of sessions
     * - Total hours
     * - Repeating Volunteers (Total)
     * - Repeating Volunteers (Percent)
     * - Double-Time Tutors (Total)
     * - Double-Time Tutors (Percent)
     */
    public function getSessionMetrics(DateTimeInterface $startDate, DateTimeInterface $endDate)
    {
    }

    /**
     * Retrieves the following statistics for each session:
     * - ID
     * - Name
     * - Date
     * - Category
     * - Total RSVP'd to attend
     * - Total attended
     */
    public function getSessionData(DateTimeInterface $startDate, DateTimeInterface $endDate)
    {
    }
}