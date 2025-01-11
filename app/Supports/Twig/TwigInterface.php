<?php

namespace App\Supports\Twig;

use ArrayIterator;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Lexer;
use Twig\Loader\LoaderInterface;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\TwigFunction;

interface TwigInterface
{
    public function addExtension(ExtensionInterface $extension): void;
    public function addRuntimeLoader(RuntimeLoaderInterface $runtimeLoader): void;
    public function addFunction(TwigFunction $function): void;
    public function setLexer(Lexer $lexer): void;
    public function addGlobal(string $name, mixed $value): void;
    public function fetch(string $template, array $data = []): string;
    public function fetchBlock(string $template, string $block, array $data = []): string;
    public function fetchFromString(string $string = '', array $data = []): string;
    public function render(string $template, array $data = [], int $status = 200): string;
    public function getLoader(): LoaderInterface;
    public function getEnvironment(): Environment;
    public function getIterator(): ArrayIterator;
}
