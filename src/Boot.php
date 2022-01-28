<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions;

use DateTime;
use InvalidArgumentException;
use SDRT\CustomFunctions\GravityForms\GravityFormsServiceProvider;
use SDRT\CustomFunctions\Support\Contracts\ServiceProvider;

use SDRT\CustomFunctions\Support\Log;

use function Sentry\init as initSentry;

class Boot
{
    /**
     * @var string[] FQCN of Service Providers
     */
    private array $serviceProviders = [
        GravityFormsServiceProvider::class,
    ];

    /**
     * Kicks off the party
     */
    public function begin(): void
    {
        $this->setupSentry();
        $this->runServiceProviders();
    }

    /**
     * Registers and boots the various Service Providers
     */
    private function runServiceProviders(): void
    {
        /** @var ServiceProvider[] $providers */
        $providers = [];
        foreach ($this->serviceProviders as $providerClass) {
            if ( ! is_subclass_of($providerClass, ServiceProvider::class)) {
                throw new InvalidArgumentException("$providerClass class must implement the ServiceProvider interface");
            }

            /** @var ServiceProvider $provider */
            $provider = new $providerClass();
            $provider->register();

            $providers[] = $provider;
        }

        foreach ($providers as $provider) {
            $provider->boot();
        }
    }

    private function setupSentry(): void
    {
        if ( ! defined('SENTRY_DSN')) {
            return;
        }

        initSentry([
            'dsn' => SENTRY_DSN,
            'environment' => wp_get_environment_type(),
        ]);
    }
}