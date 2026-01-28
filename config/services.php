<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->load('Sylius\\McpServerPlugin\\Tool\\', '../src/Tool/*')
        ->autowire()
        ->autoconfigure();

    $services->load('Sylius\\McpServerPlugin\\Loader\\', '../src/Loader/*')
        ->autowire()
        ->autoconfigure();
};
