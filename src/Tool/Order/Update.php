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

use Mcp\Capability\Attribute\McpTool;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[McpTool(
    name: 'update_order_address',
    description: 'update_order_address(tokenValue, email, billingAddress, shippingAddress?) → raw JSON object of the order after the “address” checkout step. Amount fields remain integers in minor units (÷100).',
)]
final readonly class Update
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string $tokenValue      Cart / order token obtained from create_order()
     *                                or fetch_order().  This identifies which cart
     *                                should be updated.
     * @param string $email           Customer e-mail that will be recorded on the
     *                                order (required by Sylius before moving to the
     *                                next checkout step).
     * @param array{
     *      firstName: string,
     *      lastName: string,
     *      phoneNumber: string,
     *      countryCode: string,
     *      provinceName?: string,
     *      city: string,
     *      street: string,
     *      postcode: string
     *  }            $billingAddress  Billing address object.  **All keys are
     *                                required**, except `provinceName` which is
     *                                optional in the default Sylius demo.
     * @param array{
     *      firstName?: string,
     *      lastName?: string,
     *      phoneNumber?: string,
     *      countryCode?: string,
     *      provinceName?: string,
     *      city?: string,
     *      street?: string,
     *      postcode?: string
     *  }            $shippingAddress Shipping address.  If an empty array is
     *                                supplied, the billing address will be reused
     *                                for shipping.
     */
    public function __invoke(
        string $tokenValue,
        string $email,
        array $billingAddress,
        array $shippingAddress = [],
    ): string {
        if ($shippingAddress === []) {
            $shippingAddress = $billingAddress;
        }

        $payload = [
            'email' => $email,
            'billingAddress' => $billingAddress,
            'shippingAddress' => $shippingAddress,
        ];

        $response = $this->httpClient->request(
            'PUT',
            sprintf('orders/%s', urlencode($tokenValue)),
            ['json' => $payload],
        );

        return $response->getContent();
    }
}
