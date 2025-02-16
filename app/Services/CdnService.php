<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class CdnService
{
    private $disk;

    public function __construct()
    {
        $this->disk = Storage::disk(config('cdn.disk'));
    }

    public function storeTranslations(array $translations, string $locale): string
    {
        $path = $this->getPath($locale);
        $this->disk->put($path, json_encode($translations));
        
        return $this->getUrl($path);
    }

    public function getTranslationsUrl(string $locale): ?string
    {
        $path = $this->getPath($locale);
        
        if (!$this->disk->exists($path)) {
            return null;
        }

        return $this->getUrl($path);
    }

    private function getPath(string $locale): string
    {
        return config('cdn.paths.translations') . "/{$locale}.json";
    }

    private function getUrl(string $path): string
    {
        return config('cdn.url') . '/' . $path;
    }
} 