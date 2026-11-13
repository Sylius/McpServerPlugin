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

namespace Tests\Sylius\McpServerPlugin\Unit\Controller\Shop;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Sylius\McpServerPlugin\Controller\Shop\CheckoutResumeAction;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CheckoutResumeActionTest extends TestCase
{
    /** @var OrderRepositoryInterface<OrderInterface>&MockObject */
    private OrderRepositoryInterface $orderRepository;

    private CartStorageInterface&MockObject $cartStorage;

    private UrlGeneratorInterface&MockObject $urlGenerator;

    private CheckoutResumeAction $resumeCheckout;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->cartStorage = $this->createMock(CartStorageInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $this->resumeCheckout = new CheckoutResumeAction(
            $this->orderRepository,
            $this->cartStorage,
            $this->urlGenerator,
        );
    }

    public function testItStoresTheCartForItsChannelAndRedirectsToCheckoutComplete(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $order->method('getChannel')->willReturn($channel);
        $order->method('getLocaleCode')->willReturn('pl_PL');

        $this->orderRepository->method('findCartByTokenValue')->with('TOKEN')->willReturn($order);

        $this->cartStorage->expects($this->once())->method('setForChannel')->with($channel, $order);

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('sylius_shop_checkout_complete', ['_locale' => 'pl_PL'])
            ->willReturn('/pl_PL/checkout/complete')
        ;

        $response = ($this->resumeCheckout)('TOKEN');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/pl_PL/checkout/complete', $response->getTargetUrl());
    }

    public function testItFallsBackToTheDefaultLocaleWhenTheOrderHasNone(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $order->method('getChannel')->willReturn($this->createMock(ChannelInterface::class));
        $order->method('getLocaleCode')->willReturn(null);

        $this->orderRepository->method('findCartByTokenValue')->willReturn($order);

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('sylius_shop_checkout_complete', ['_locale' => 'en_US'])
            ->willReturn('/en_US/checkout/complete')
        ;

        ($this->resumeCheckout)('TOKEN');
    }

    public function testItThrowsNotFoundWhenNoCartMatchesTheToken(): void
    {
        $this->orderRepository->method('findCartByTokenValue')->with('UNKNOWN')->willReturn(null);

        $this->cartStorage->expects($this->never())->method('setForChannel');

        $this->expectException(NotFoundHttpException::class);

        ($this->resumeCheckout)('UNKNOWN');
    }

    public function testItThrowsNotFoundWhenTheCartHasNoChannel(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $order->method('getChannel')->willReturn(null);

        $this->orderRepository->method('findCartByTokenValue')->willReturn($order);

        $this->cartStorage->expects($this->never())->method('setForChannel');

        $this->expectException(NotFoundHttpException::class);

        ($this->resumeCheckout)('TOKEN');
    }
}
