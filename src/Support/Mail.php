<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Support;

class Mail
{
    public static function send(string $to, string $subject, $message, array $headers = []): bool
    {
        if (is_array($message)) {
            $message = self::formatMessage($message);
        }

        return wp_mail($to, $subject, $message, array_merge($headers, [
            'From: SD Refugee Tutoring <info@sdrefugeetutoring.com>',
            'Reply-To: SD Refugee Tutoring <info@sdrefugeetutoring.com>',
            'Content-Type: text/html',
        ]));
    }

    /**
     * @param array{option: string, fname: string, event_title: string} $options
     *
     * @return string
     */
    public static function formatMessage(array $options): string
    {
        $body = isset($options['option']) ? get_option($options['option'] ) : '';

        $registered_tags = array(
            '{first_name}'    => $options['fname'] ?? '',
            '{event_title}'   => $options['event_title'] ?? '',
            '{lost_password_link}'  => '<a href="' . wp_lostpassword_url( get_home_url('', '/events/') ) . '" title="Lost Password">Reset Your Password Here</a>',
        );

        preg_match_all( "/{([A-z0-9\-\_]+)}/s", $body, $existing_tags );

        $final_array = array_intersect_key( $registered_tags, array_flip($existing_tags[0]) );

        $new_content = str_replace( $existing_tags[0], $final_array, $body );

        return wpautop( $new_content );
    }
}