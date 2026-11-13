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

namespace Tests\Sylius\McpServerPlugin\Unit\HttpClient;

use PHPUnit\Framework\TestCase;
use Sylius\McpServerPlugin\HttpClient\RequestAwareShopApiClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final class RequestAwareShopApiClientTest extends TestCase
{
    public function testItDerivesBaseUriFromTheCurrentRequest(): void
    {
        $decorated = $this->createMock(HttpClientInterface::class);
        $decorated
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'products/mug', ['base_uri' => 'https://shop.example.com/api/v2/shop/'])
            ->willReturn($this->createMock(ResponseInterface::class))
        ;

        $client = new RequestAwareShopApiClient($decorated, $this->requestStackFor('https://shop.example.com/some/page'));

        $client->request('GET', 'products/mug');
    }

    public function testItKeepsTheNonStandardPortOfTheCurrentRequest(): void
    {
        $decorated = $this->createMock(HttpClientInterface::class);
        $decorated
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'products/mug', ['base_uri' => 'http://localhost:8080/api/v2/shop/'])
            ->willReturn($this->createMock(ResponseInterface::class))
        ;

        $client = new RequestAwareShopApiClient($decorated, $this->requestStackFor('http://localhost:8080/some/page'));

        $client->request('GET', 'products/mug');
    }

    public function testItDoesNotOverwriteAnExplicitlyPassedBaseUri(): void
    {
        $decorated = $this->createMock(HttpClientInterface::class);
        $decorated
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'products/mug', ['base_uri' => 'https://other.example.com/api/v2/shop/'])
            ->willReturn($this->createMock(ResponseInterface::class))
        ;

        $client = new RequestAwareShopApiClient($decorated, $this->requestStackFor('https://shop.example.com/some/page'));

        $client->request('GET', 'products/mug', ['base_uri' => 'https://other.example.com/api/v2/shop/']);
    }

    public function testItLeavesOptionsUntouchedWithoutACurrentRequest(): void
    {
        $decorated = $this->createMock(HttpClientInterface::class);
        $decorated
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'products/mug', ['timeout' => 2.0])
            ->willReturn($this->createMock(ResponseInterface::class))
        ;

        $client = new RequestAwareShopApiClient($decorated, new RequestStack());

        $client->request('GET', 'products/mug', ['timeout' => 2.0]);
    }

    public function testItPreservesTheRemainingOptionsWhileInjectingBaseUri(): void
    {
        $decorated = $this->createMock(HttpClientInterface::class);
        $decorated
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'products/mug', [
                'timeout' => 2.0,
                'base_uri' => 'https://shop.example.com/api/v2/shop/',
            ])
            ->willReturn($this->createMock(ResponseInterface::class))
        ;

        $client = new RequestAwareShopApiClient($decorated, $this->requestStackFor('https://shop.example.com/some/page'));

        $client->request('GET', 'products/mug', ['timeout' => 2.0]);
    }

    public function testWithOptionsReturnsACloneDelegatingToTheReconfiguredClient(): void
    {
        $reconfigured = $this->createMock(HttpClientInterface::class);
        $reconfigured
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->createMock(ResponseInterface::class))
        ;

        $decorated = $this->createMock(HttpClientInterface::class);
        $decorated->method('withOptions')->with(['timeout' => 2.0])->willReturn($reconfigured);
        $decorated->expects($this->never())->method('request');

        $client = new RequestAwareShopApiClient($decorated, new RequestStack());
        $clone = $client->withOptions(['timeout' => 2.0]);

        $this->assertNotSame($client, $clone);

        $clone->request('GET', 'products/mug');
    }

    public function testItDelegatesStreaming(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $stream = $this->createMock(ResponseStreamInterface::class);

        $decorated = $this->createMock(HttpClientInterface::class);
        $decorated
            ->expects($this->once())
            ->method('stream')
            ->with([$response], 1.5)
            ->willReturn($stream)
        ;

        $client = new RequestAwareShopApiClient($decorated, new RequestStack());

        $this->assertSame($stream, $client->stream([$response], 1.5));
    }

    private function requestStackFor(string $uri): RequestStack
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create($uri));

        return $requestStack;
    }
}
