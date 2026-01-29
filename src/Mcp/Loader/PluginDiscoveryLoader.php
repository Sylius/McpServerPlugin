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

namespace Sylius\McpServerPlugin\Mcp\Loader;

use Mcp\Capability\Discovery\Discoverer;
use Mcp\Capability\Registry\Loader\LoaderInterface;
use Mcp\Capability\RegistryInterface;
use Psr\Log\LoggerInterface;

final readonly class PluginDiscoveryLoader implements LoaderInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function load(RegistryInterface $registry): void
    {
        $discoverer = new Discoverer($this->logger);
        $state = $discoverer->discover(
            basePath: dirname(__DIR__, 3),
            directories: ['src/Tool'],
        );

        foreach ($state->getTools() as $toolRef) {
            $registry->registerTool(
                $toolRef->tool,
                $toolRef->handler,
                isManual: true,
            );
        }
    }
}
