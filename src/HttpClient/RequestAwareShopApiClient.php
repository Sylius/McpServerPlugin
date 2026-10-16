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

namespace Sylius\McpServerPlugin\HttpClient;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final class RequestAwareShopApiClient implements HttpClientInterface
{
    private const API_PATH = '/api/v2/shop/';

    public function __construct(
        private HttpClientInterface $client,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        // base_uri must be passed in per-request $options, not via withOptions().
        // ScopingHttpClient::withOptions() only clones the inner transport and does not update
        // $defaultOptionsByRegexp used for relative URL resolution, so a dynamic base_uri set
        // through withOptions() would be silently ignored.
        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null && !isset($options['base_uri'])) {
            $options['base_uri'] = $request->getSchemeAndHttpHost() . self::API_PATH;
        }

        return $this->client->request($method, $url, $options);
    }

    public function stream(iterable|ResponseInterface $responses, ?float $timeout = null): ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }

    public function withOptions(array $options): static
    {
        $clone = clone $this;
        $clone->client = $this->client->withOptions($options);

        return $clone;
    }
}
