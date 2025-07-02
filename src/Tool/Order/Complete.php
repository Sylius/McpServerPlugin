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

namespace Sylius\McpServerPlugin\Tool\Order;

use PhpMcp\Server\Attributes\McpTool;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[McpTool(
    name: 'complete_checkout',
    description: 'complete_checkout(tokenValue, notes?) → raw JSON order after finishing checkout. The response still contains `"tokenValue"`; ChatGPT SHOULD always return that token to the customer so they can look up the order later.',
)]
final readonly class Complete
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string $tokenValue Cart/order token from create_order / fetch_order.
     * @param string $notes      Optional customer notes for the order.
     */
    public function __invoke(string $tokenValue, string $notes = ''): string
    {
        $response = $this->httpClient->request(
            'PATCH',
            sprintf('orders/%s/complete', urlencode($tokenValue)),
            [
                'json' => ['notes' => $notes],
            ],
        );

        return $response->getContent();
    }
}
