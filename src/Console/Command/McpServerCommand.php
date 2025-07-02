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

namespace Sylius\McpServerPlugin\Console\Command;

use Sylius\McpServerPlugin\Factory\ServerFactoryInterface;
use Sylius\McpServerPlugin\Factory\ServerTransportFactoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'sylius:mcp-server:start',
    description: 'Start the Sylius MCP server',
)]
final class McpServerCommand extends Command
{
    public function __construct(
        private readonly ServerFactoryInterface $serverFactory,
        private readonly ServerTransportFactoryInterface $serverTransportFactory,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Starting MCP Server</info>');

        $server = $this->serverFactory->create();
        $transport = $this->serverTransportFactory->create();

        $server->listen($transport);

        return Command::SUCCESS;
    }
}
