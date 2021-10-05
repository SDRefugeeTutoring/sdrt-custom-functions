<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Support\Contracts;

interface ServiceProvider
{
    /**
     * Registers things with the Service Container. Do not run code or hook into things here.
     */
    public function register(): void;

    /**
     * Boots the service to do the things it should do. Run code and hook into things here.
     */
    public function boot(): void;
}