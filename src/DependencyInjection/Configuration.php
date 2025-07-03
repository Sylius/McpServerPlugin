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

namespace Sylius\McpServerPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_mcp_server');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('server')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('name')->defaultValue('Sylius MCP Server')->end()
                        ->scalarNode('version')->defaultValue('0.1.0')->end()

                        ->arrayNode('transport')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('host')->defaultValue('127.0.0.1')->end()
                                ->integerNode('port')->defaultValue(8080)->end()
                                ->scalarNode('prefix')->defaultValue('mcp')->end()
                                ->booleanNode('enable_json_response')->defaultFalse()->end()
                                ->arrayNode('ssl')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->booleanNode('enabled')->defaultFalse()->end()
                                        ->variableNode('context')
                                            ->defaultValue([])
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('session')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->enumNode('driver')->values(['array', 'cache'])->defaultValue('cache')->end()
                                ->integerNode('ttl')->defaultValue(3600)->end()
                            ->end()
                        ->end()

                        ->arrayNode('discovery')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('locations')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('base_path')->isRequired()->cannotBeEmpty()->end()
                                            ->arrayNode('scan_dirs')
                                                ->prototype('scalar')->end()
                                                ->requiresAtLeastOneElement()
                                            ->end()
                                        ->end()
                                    ->end()
                                    ->beforeNormalization()
                                        ->always(function ($v) {
                                            $v[] = [
                                                'base_path' => '%sylius_mcp_server.plugin_root%',
                                                'scan_dirs' => ['src/Tool'],
                                            ];
                                            return $v;
                                        })
                                    ->end()
                                ->end()
                            ->end()
                        ->end()

                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
