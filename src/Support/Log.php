<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Support;

use Sentry\Severity;
use Sentry\State\Scope;

use function Sentry\captureMessage;
use function Sentry\withScope;

class Log
{
    public static function fatal(string $message, array $context = []): void
    {
        self::sendToSentry($message, $context, Severity::fatal());
    }

    public static function error(string $message, array $context = []): void
    {
        self::sendToSentry($message, $context, Severity::error());
    }

    public static function warning(string $message, array $context = []): void
    {
        self::sendToSentry($message, $context, Severity::warning());
    }

    public static function info(string $message, array $context = []): void
    {
        self::sendToSentry($message, $context, Severity::info());
    }

    public static function debug(string $message, array $context = []): void
    {
        self::sendToSentry($message, $context, Severity::debug());
    }

    private static function sendToSentry(string $message, array $context, Severity $severity): void
    {
        if ( ! self::sentryIsEnabled()) {
            return;
        }

        withScope(function (Scope $scope) use ($message, $context, $severity): void {
            $scope->setContext('Context', $context);
            $scope->setLevel($severity);

            if (function_exists('is_user_logged_in') && is_user_logged_in() && ($user = wp_get_current_user())) {
                $scope->setUser([
                    'id' => $user->ID,
                    'username' => $user->user_login,
                    'email' => wp_get_current_user()->user_email,
                ]);
            }

            captureMessage("Log: $message");
        });
    }

    private static function sentryIsEnabled(): bool
    {
        return function_exists('Sentry\\captureMessage') && defined('SENTRY_DSN');
    }
}