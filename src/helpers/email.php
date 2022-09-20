<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Helpers\Email;

/**
 * Sends an email with the usual SDRT headers and functions
 *
 * @param string       $to
 * @param string       $subject
 * @param string|array $message when an array, use the sdrt_send_email function to get content
 * @param array        $headers
 *
 * @return bool
 */
function mail(string $to, string $subject, $message, array $headers = []): bool
{
    if (is_array($message)) {
        $message = sdrt_send_email($message);
    }

    return wp_mail($to, $subject, $message, array_merge($headers, [
        'From: SD Refugee Tutoring <info@sdrefugeetutoring.com>',
        'Reply-To: SD Refugee Tutoring <info@sdrefugeetutoring.com>',
        'Content-Type: text/html; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ]));
}
