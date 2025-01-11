<?php

namespace App\Supports\Twig\Extensions;

use Exception;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class ViteExtension extends AbstractExtension
{
    private const string VITE_MANIFEST_FILE = '.vite/manifest.json';
    private const string VITE_CLIENT_SCRIPT = '@vite/client';
    private string $buildDirectory = 'build';
    private string $manifestPath;
    private string $viteServerUrl;

    public function __construct()
    {
        $this->manifestPath = base_path("public/{$this->buildDirectory}/" . self::VITE_MANIFEST_FILE);
        $this->viteServerUrl = env('APP_VITE_SERVER');
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite', [$this, 'generateViteAssetsHtml'], ['is_safe' => ['html']]),
        ];
    }

    public function generateViteAssetsHtml(array $assets): string
    {
        $html = $this->generateViteClientModuleHtml();
        foreach ($assets as $asset) {
            $html .= $this->isAssetOnViteServer($asset)
                ? $this->renderAssetFromViteServer($asset)
                : $this->renderAssetFromManifest($asset);
        }

        return $html;
    }

    private function isAssetOnViteServer(string $asset): bool
    {
        static $assetCache = [];
        return $assetCache[$asset] ??= $this->checkIfAssetExistsOnViteServer($asset);
    }

    private function checkIfAssetExistsOnViteServer(string $asset): bool
    {
        $url = $this->buildViteServerAssetUrl($asset);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_exec($curl);
        $isError = curl_errno($curl);
        curl_close($curl);

        return !$isError;
    }

    /** @throws Exception */
    private function getManifestContents(): array
    {
        static $manifestCache = null;
        return $manifestCache ??= $this->loadManifestFile();
    }

    /** @throws Exception */
    private function loadManifestFile(): array
    {
        if (!is_file($this->manifestPath)) {
            throw new Exception("Vite manifest not found at: {$this->manifestPath}");
        }

        return json_decode(file_get_contents($this->manifestPath), true);
    }

    private function buildManifestAssetUrl(string $asset): string
    {
        return "{$this->buildDirectory}/" . ($this->getManifestContents()[$asset]['file'] ?? '');
    }

    private function buildViteServerAssetUrl(string $asset): string
    {
        return rtrim($this->viteServerUrl, '/') . '/' . ltrim($asset, '/');
    }

    private function renderAsset(string $asset, string $url): string
    {
        return match (pathinfo($asset, PATHINFO_EXTENSION)) {
            'js', 'mjs' => $this->generateScriptTag($url),
            'css', 'scss' => $this->generateLinkTag($url),
            default => '',
        };
    }

    private function renderAssetFromViteServer(string $asset): string
    {
        return $this->renderAsset($asset, $this->buildViteServerAssetUrl($asset));
    }

    private function renderAssetFromManifest(string $asset): string
    {
        return $this->renderAsset($asset, $this->buildManifestAssetUrl($asset));
    }

    private function generateScriptTag(string $src): string
    {
        return new Markup("<script type=\"module\" crossorigin src=\"{$src}\"></script>\n", 'UTF-8');
    }

    private function generateLinkTag(string $href): string
    {
        return new Markup("<link rel=\"stylesheet\" href=\"{$href}\">\n", 'UTF-8');
    }

    private function generateViteClientModuleHtml(): string
    {
        return $this->isAssetOnViteServer(self::VITE_CLIENT_SCRIPT)
            ? $this->generateScriptTag($this->buildViteServerAssetUrl(self::VITE_CLIENT_SCRIPT))
            : '';
    }
}
