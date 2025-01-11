<?php
declare(strict_types=1);

namespace App\Supports;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Slim\Routing\UrlGenerator;

final readonly class Redirect
{
    private ?ResponseInterface $response;
    private ?ServerRequestInterface $request;
    private ?UrlGenerator $urlGenerator;

    public function __construct(ResponseInterface $response, ServerRequestInterface $request, UrlGenerator $urlGenerator)
    {
        $this->response = $response;
        $this->request = $request;
        $this->urlGenerator = $urlGenerator;
    }

    private function generateSafeUrl(string $routeName, array $data = [], array $queryParams = []): string
    {
        try {
            return $this->urlGenerator->relativeUrlFor($routeName, $data, $queryParams);
        } catch (RuntimeException) {
            return $routeName;
        }
    }

    public function to(string $routeName, array $data = [], array $queryParams = [], int $status = 302): ResponseInterface
    {
        return $this->response
            ->withHeader('Location', $this->generateSafeUrl($routeName, $data, $queryParams))
            ->withStatus($status);
    }

    public function back(): ResponseInterface
    {
        return $this->to((string) $this->request->getUri());
    }
}
