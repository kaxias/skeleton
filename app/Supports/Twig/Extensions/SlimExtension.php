<?php

namespace App\Supports\Twig\Extensions;

use DI\Attribute\Inject;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\UrlGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SlimExtension extends AbstractExtension
{
    #[Inject]
    private ?UrlGenerator $urlGenerator = null;

    #[Inject]
    private ?ServerRequestInterface $request = null;

    public function getFunctions(): array
    {
        return [
            new TwigFunction('url_for', [$this, 'urlFor'], ['is_safe' => ['html']]),
            new TwigFunction('full_url_for', [$this, 'fullUrlFor'], ['is_safe' => ['html']]),
            new TwigFunction('is_current_url', [$this, 'isCurrentUrl'], ['is_safe' => ['html']]),
            new TwigFunction('current_url', [$this, 'getCurrentUrl'], ['is_safe' => ['html']]),
        ];
    }

    public function urlFor(string $routeName, array $data = [], array $queryParams = []): string
    {
        return $this->urlGenerator->urlFor($routeName, $data, $queryParams);
    }

    public function fullUrlFor(string $routeName, array $data = [], array $queryParams = []): string
    {
        return $this->urlGenerator->fullUrlFor($this->request->getUri(), $routeName, $data, $queryParams);
    }

    public function isCurrentUrl(string $routeName, array $data = []): bool
    {
        return $this->urlGenerator->urlFor($routeName, $data) === $this->request->getUri()->getPath();
    }

    public function getCurrentUrl(bool $withQueryString = false): string
    {
        $currentUrl = $this->request->getUri()->getPath();
        $query = $this->request->getUri()->getQuery();

        if ($withQueryString && !empty($query)) {
            $currentUrl .= '?' . $query;
        }

        return $currentUrl;
    }
}
