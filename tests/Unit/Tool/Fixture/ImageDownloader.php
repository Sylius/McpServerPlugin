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

namespace Tests\Sylius\McpServerPlugin\Unit\Tool\Fixture;

use Mcp\Schema\Content\Content;
use Sylius\McpServerPlugin\Tool\DownloadsImagesTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Exposes the trait the way the real tools use it: a readonly class with an
 * $httpClient property, see Sylius\McpServerPlugin\Tool\Product\Fetch.
 */
final readonly class ImageDownloader
{
    use DownloadsImagesTrait;

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string[] $urls
     *
     * @return Content[]
     */
    public function __invoke(array $urls): array
    {
        return $this->downloadImages($urls);
    }
}
