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

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    if (str_starts_with($container->env(), 'test')) {
        // Sylius 2.3 ships its Behat services as a PHP config; 2.2 only as XML.
        $syliusBehatServices = '../../../vendor/sylius/sylius/src/Sylius/Behat/Resources/config/services';
        $container->import(is_file(__DIR__ . '/' . $syliusBehatServices . '.php') ? $syliusBehatServices . '.php' : $syliusBehatServices . '.xml');
        $container->import('@SyliusMcpServerPlugin/tests/Behat/Resources/services.php');
    }
};
