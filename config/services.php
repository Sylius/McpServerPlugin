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

use Sylius\McpServerPlugin\Mcp\Loader\PluginDiscoveryLoader;
use Sylius\McpServerPlugin\Tool\Channel\Fetch as ChannelFetch;
use Sylius\McpServerPlugin\Tool\Currency\Fetch as CurrencyFetch;
use Sylius\McpServerPlugin\Tool\Order\AddItem;
use Sylius\McpServerPlugin\Tool\Order\Complete;
use Sylius\McpServerPlugin\Tool\Order\Create;
use Sylius\McpServerPlugin\Tool\Order\Fetch as OrderFetch;
use Sylius\McpServerPlugin\Tool\Order\PaymentMethodList;
use Sylius\McpServerPlugin\Tool\Order\PaymentMethodSelect;
use Sylius\McpServerPlugin\Tool\Order\ShippingMethodList;
use Sylius\McpServerPlugin\Tool\Order\ShippingMethodSelect;
use Sylius\McpServerPlugin\Tool\Order\Update;
use Sylius\McpServerPlugin\Tool\Product\Fetch as ProductFetch;
use Sylius\McpServerPlugin\Tool\Product\Search as ProductSearch;
use Sylius\McpServerPlugin\Tool\ProductVariant\Fetch as ProductVariantFetch;
use Sylius\McpServerPlugin\Tool\ProductVariant\Search as ProductVariantSearch;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(PluginDiscoveryLoader::class)
        ->args([service('logger')])
        ->tag('monolog.logger', ['channel' => 'mcp'])
        ->tag('mcp.loader');

    $services->set(ChannelFetch::class)
        ->args([service('sylius_mcp_server.http_client.api_shop')])
        ->tag('mcp.tool');

    $services->set(CurrencyFetch::class)
        ->args([service('sylius_mcp_server.http_client.api_shop')])
        ->tag('mcp.tool');

    $services->set(AddItem::class)
        ->args([service('sylius_mcp_server.http_client.api_shop')])
        ->tag('mcp.tool');

    $services->set(Create::class)
        ->args([service('sylius_mcp_server.http_client.api_shop')])
        ->tag('mcp.tool');

    $services->set(OrderFetch::class)
        ->args([service('sylius_mcp_server.http_client.api_shop')])
        ->tag('mcp.tool');

    $services->set(PaymentMethodList::class)
        ->args([service('sylius_mcp_server.http_client.api_shop')])
        ->tag('mcp.tool');

    $services->set(ShippingMethodList::class)
        ->args([service('sylius_mcp_server.http_client.api_shop')])
        ->tag('mcp.tool');

    $services->set(Update::class)
        ->args([service('sylius_mcp_server.http_client.api_shop')])
        ->tag('mcp.tool');

    $services->set(ProductFetch::class)
        ->args([service('sylius_mcp_server.http_client.api_shop')])
        ->tag('mcp.tool');

    $services->set(ProductSearch::class)
        ->args([service('sylius_mcp_server.http_client.api_shop')])
        ->tag('mcp.tool');

    $services->set(ProductVariantFetch::class)
        ->args([service('sylius_mcp_server.http_client.api_shop')])
        ->tag('mcp.tool');

    $services->set(ProductVariantSearch::class)
        ->args([service('sylius_mcp_server.http_client.api_shop')])
        ->tag('mcp.tool');

    $services->set(Complete::class)
        ->args([service('sylius_mcp_server.http_client.api_shop_merge_patch')])
        ->tag('mcp.tool');

    $services->set(PaymentMethodSelect::class)
        ->args([service('sylius_mcp_server.http_client.api_shop_merge_patch')])
        ->tag('mcp.tool');

    $services->set(ShippingMethodSelect::class)
        ->args([service('sylius_mcp_server.http_client.api_shop_merge_patch')])
        ->tag('mcp.tool');
};
