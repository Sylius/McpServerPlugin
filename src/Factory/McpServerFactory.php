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

namespace Sylius\McpServerPlugin\Factory;

use PhpMcp\Server\Server;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Psr16Cache;

final readonly class McpServerFactory implements ServerFactoryInterface
{

    /**
     * @param 'array'|'cache' $sessionDriver
     */
    public function __construct(
        private ContainerInterface $container,
        private CacheItemPoolInterface $cache,
        private LoggerInterface $logger,
        private string $name,
        private string $version,
        private string $sessionDriver,
        private int $sessionTtl,
        private array $discoveryLocations,
    ) {
    }

    public function create(): Server
    {
        $server = Server::make()
            ->withServerInfo(
                name: $this->name,
                version: $this->version,
            )
            ->withLogger($this->logger)
            ->withContainer($this->container)
            ->withCache(new Psr16Cache($this->cache))
            ->withSession(
                driver: $this->sessionDriver,
                ttl: $this->sessionTtl,
            )
            ->build()
        ;

        foreach ($this->discoveryLocations as $discoveryLocation) {
            $server->discover(
                basePath: $discoveryLocation['base_path'],
                scanDirs: $discoveryLocation['scan_dirs'],
            );
        }

        return $server;
    }
}
