<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Support;

use DI\DependencyException;
use DI\NotFoundException;
use InvalidArgumentException;

class Hooks
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function addFilter(
        string $tag,
        string $class,
        string $method = '__invoke',
        int $priority = 10,
        int $acceptedArgs = 1
    ): void {
        if ( ! method_exists($class, $method)) {
            throw new InvalidArgumentException("The method $method does not exist on $class");
        }

        add_filter(
            $tag,
            static function () use ($class, $method) {
                $instance = sdrt($class);

                $instance->$method(...func_get_args());
            },
            $priority,
            $acceptedArgs
        );
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function addAction(
        string $tag,
        string $class,
        string $method = '__invoke',
        int $priority = 10,
        int $acceptedArgs = 1
    ): void {
        if ( ! method_exists($class, $method)) {
            throw new InvalidArgumentException("The method $method does not exist on $class");
        }

        add_action(
            $tag,
            static function () use ($class, $method) {
                $instance = sdrt($class);

                $instance->$method(...func_get_args());
            },
            $priority,
            $acceptedArgs
        );
    }
}