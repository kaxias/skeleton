<?php

declare(strict_types=1);

namespace App\Supports;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class View
{
    public function __construct(private ResponseInterface $response, private Twig\TwigInterface $twig, private SerializerInterface $serializer)
    {
    }

    public function twig(string $template, array $data = [], int $status = 200): ResponseInterface
    {
        $this->response->getBody()->write($this->twig->render($template, $data));
        $this->response->withHeader('Content-Type', $this->contentType('html'));

        return $this->response->withStatus($status);
    }

    /**
     * @param array|object $data |=> array data
     * @param array $context     |=> see this page for more info https://symfony.com/doc/current/serializer.html
     * @param int $status        |=> status code default 200
     * @param string $format     |=> default format [json] available [json, xml]
     * @return ResponseInterface
     */
    public function json(array|object $data, array $context = [], int $status = 200, string $format = 'json'): ResponseInterface
    {
        $this->response->getBody()->write($this->serializer->serialize($data, $format, $context));
        $this->response->withHeader('Content-Type', $this->contentType($format));

        return $this->response->withStatus($status);
    }

    private function contentType(string $type): string
    {
        $contentType['html'] = 'text/html; charset=utf-8';
        $contentType['json'] = 'application/ld+json; charset=utf-8';
        $contentType['xml'] = 'application/xhtml+xml; charset=utf-8';

        return $contentType[$type];
    }
}
