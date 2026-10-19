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

namespace Sylius\McpServerPlugin\Controller\Shop;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CheckoutResumeAction
{
    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CartStorageInterface $cartStorage,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(string $tokenValue): Response
    {
        $order = $this->orderRepository->findCartByTokenValue($tokenValue);

        if (!$order instanceof OrderInterface) {
            throw new NotFoundHttpException();
        }

        $channel = $order->getChannel();
        if ($channel === null) {
            throw new NotFoundHttpException();
        }

        $this->cartStorage->setForChannel($channel, $order);

        $locale = $order->getLocaleCode() ?? 'en_US';

        $url = $this->urlGenerator->generate('sylius_shop_checkout_complete', ['_locale' => $locale]);

        return new RedirectResponse($url);
    }
}
