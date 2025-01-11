<?php

declare(strict_types=1);

namespace App\Supports\Twig;

use ArrayIterator;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Lexer;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\TwigFunction;

class Twig implements TwigInterface
{
    private readonly LoaderInterface $loader;
    private readonly Environment $environment;
    private array $defaultVariables = [];

    public function __construct(string|array $paths, array $settings = [])
    {
        $this->environment = new Environment($this->loader($paths), $settings);
    }

    private function loader(string|array $path): FilesystemLoader
    {
        $loader = new FilesystemLoader();
        $paths = is_array($path) ? $path : [$path];

        foreach ($paths as $namespace => $path) {
            if (is_string($namespace)) {
                $loader->setPaths($path, $namespace);
            } else {
                $loader->addPath($path);
            }
        }

        $this->setLoader($loader);

        return $loader;
    }

    private function setLoader(LoaderInterface $loader): void
    {
        $this->loader = $loader;
    }

    public function addExtension(ExtensionInterface $extension): void
    {
        $this->environment->addExtension($extension);
    }

    public function addRuntimeLoader(RuntimeLoaderInterface $runtimeLoader): void
    {
        $this->environment->addRuntimeLoader($runtimeLoader);
    }

    public function addFunction(TwigFunction $function): void
    {
        $this->environment->addFunction($function);
    }

    public function setLexer(Lexer $lexer): void
    {
        $this->environment->setLexer($lexer);
    }

    public function addGlobal(string $name, mixed $value): void
    {
        $this->environment->addGlobal($name, $value);
    }

    public function fetch(string $template, array $data = []): string
    {
        $data = array_merge($this->defaultVariables, $data);

        return $this->environment->render($template, $data);
    }

    public function fetchBlock(string $template, string $block, array $data = []): string
    {
        $data = array_merge($this->defaultVariables, $data);

        return $this->environment->resolveTemplate($template)->renderBlock($block, $data);
    }

    public function fetchFromString(string $string = '', array $data = []): string
    {
        $data = array_merge($this->defaultVariables, $data);

        return $this->environment->createTemplate($string)->render($data);
    }

    public function render(string $template, array $data = [], int $status = 200): string
    {
        return $this->fetch($template, $data);
    }

    public function getLoader(): LoaderInterface
    {
        return $this->loader;
    }

    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->defaultVariables);
    }
}
