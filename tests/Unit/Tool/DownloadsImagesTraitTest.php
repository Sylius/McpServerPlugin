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

namespace Tests\Sylius\McpServerPlugin\Unit\Tool;

use Mcp\Schema\Content\ImageContent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Tests\Sylius\McpServerPlugin\Unit\Tool\Fixture\ImageDownloader;

final class DownloadsImagesTraitTest extends TestCase
{
    public function testItBase64EncodesEachDownloadedImage(): void
    {
        $downloadImages = new ImageDownloader(new MockHttpClient([
            new MockResponse('raw-png-bytes', ['response_headers' => ['content-type' => 'image/png']]),
        ]));

        $contents = $downloadImages(['https://shop.example.com/media/mug.png']);

        $this->assertCount(1, $contents);
        $this->assertInstanceOf(ImageContent::class, $contents[0]);
        $this->assertSame(base64_encode('raw-png-bytes'), $contents[0]->data);
        $this->assertSame('image/png', $contents[0]->mimeType);
    }

    public function testItStripsParametersFromTheContentTypeHeader(): void
    {
        $downloadImages = new ImageDownloader(new MockHttpClient([
            new MockResponse('raw-jpeg-bytes', ['response_headers' => ['content-type' => 'image/jpeg; charset=binary']]),
        ]));

        $contents = $downloadImages(['https://shop.example.com/media/mug.jpg']);

        $this->assertInstanceOf(ImageContent::class, $contents[0]);
        $this->assertSame('image/jpeg', $contents[0]->mimeType);
    }

    public function testItFallsBackToWebpWhenTheContentTypeHeaderIsMissing(): void
    {
        $downloadImages = new ImageDownloader(new MockHttpClient([
            new MockResponse('raw-bytes', ['response_headers' => []]),
        ]));

        $contents = $downloadImages(['https://shop.example.com/media/mug']);

        $this->assertInstanceOf(ImageContent::class, $contents[0]);
        $this->assertSame('image/webp', $contents[0]->mimeType);
    }

    public function testItSkipsImagesThatFailToDownloadAndKeepsTheRest(): void
    {
        $downloadImages = new ImageDownloader(new MockHttpClient([
            new MockResponse('', ['http_code' => 500]),
            new MockResponse('raw-png-bytes', ['response_headers' => ['content-type' => 'image/png']]),
        ]));

        $contents = $downloadImages([
            'https://shop.example.com/media/broken.png',
            'https://shop.example.com/media/mug.png',
        ]);

        $this->assertCount(1, $contents);
        $this->assertInstanceOf(ImageContent::class, $contents[0]);
        $this->assertSame(base64_encode('raw-png-bytes'), $contents[0]->data);
    }

    public function testItIssuesNoRequestForAnEmptyUrlList(): void
    {
        $client = new MockHttpClient([]);
        $downloadImages = new ImageDownloader($client);

        $this->assertSame([], $downloadImages([]));
        $this->assertSame(0, $client->getRequestsCount());
    }
}
