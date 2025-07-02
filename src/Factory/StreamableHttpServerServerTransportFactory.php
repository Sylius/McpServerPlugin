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

use PhpMcp\Server\Contracts\ServerTransportInterface;
use PhpMcp\Server\Transports\StreamableHttpServerTransport;

final readonly class StreamableHttpServerServerTransportFactory implements ServerTransportFactoryInterface
{
    public function __construct(
        private string $host,
        private int $port,
        private string $prefix,
        private bool $enableJsonResponse,
        private bool $sslEnabled,
        private array $sslContext,
    ) {
    }

    public function create(): ServerTransportInterface
    {
        return new StreamableHttpServerTransport(
            host: $this->host,
            port: $this->port,
            mcpPath: $this->prefix,
            sslContext: $this->sslEnabled ? $this->sslContext : null,
            enableJsonResponse: $this->enableJsonResponse,
        );
    }
}
