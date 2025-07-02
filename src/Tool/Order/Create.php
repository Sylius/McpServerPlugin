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
    name: 'create_order',
    description: 'create_order(ignore?) → raw JSON object for a brand-new Sylius order (state “cart”). Store the tokenValue field and pass it to other cart-management tools.',
)]
final readonly class Create
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    public function __invoke(): string
    {
        $response = $this->httpClient->request(
            'POST',
            'orders',
            [
                'json' => [],
            ],
        );

        return $response->getContent();
    }
}
