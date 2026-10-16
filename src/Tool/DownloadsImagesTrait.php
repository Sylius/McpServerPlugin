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

namespace Sylius\McpServerPlugin\Tool;

use Mcp\Schema\Content\Content;
use Mcp\Schema\Content\ImageContent;

trait DownloadsImagesTrait
{
    /**
     * @param string[] $urls
     *
     * @return Content[]
     */
    private function downloadImages(array $urls): array
    {
        $responses = array_map(fn (string $url) => $this->httpClient->request('GET', $url), $urls);
        $contents = [];

        foreach ($responses as $response) {
            try {
                $data = $response->getContent();
                $mimeType = explode(';', $response->getHeaders()['content-type'][0] ?? 'image/webp')[0];
                $contents[] = ImageContent::fromString($data, trim($mimeType));
            } catch (\Throwable) {
            }
        }

        return $contents;
    }
}
