<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Support;

use Sentry\State\Scope;

use function Sentry\captureMessage;
use function Sentry\withScope;

class Log
{
    public static function error(string $message, array $context = []): void
    {
        if ( ! self::sentryIsEnabled()) {
            return;
        }

        withScope(function (Scope $scope) use ($message, $context): void {
            $scope->setContext('Context', $context);

            captureMessage("Log: $message");
        });
    }

    private static function sentryIsEnabled(): bool
    {
        return function_exists('Sentry\\captureMessage') && defined('SENTRY_DSN');
    }
}