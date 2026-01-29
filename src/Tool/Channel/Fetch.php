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

namespace Sylius\McpServerPlugin\Tool\Channel;

use Mcp\Capability\Attribute\McpTool;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[McpTool(
    name: 'fetch_channel',
    description: 'fetch_channel(ignore?) → raw JSON Hydra collection containing exactly one Sylius channel. The single member includes code, name and baseCurrency IRI; call fetch_currency to obtain the ISO currency code.',
)]
final readonly class Fetch
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    public function __invoke(): string
    {
        $response = $this->httpClient->request('GET', 'channels');

        return $response->getContent();
    }
}
