<?php

namespace App\Services;

use App\Models\Translation;
use App\Models\Language;
use App\Models\Tag;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TranslationService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const PER_PAGE = 50;

    public function getTranslations(string $locale, array $tags = [], ?int $page = null): LengthAwarePaginator
    {
        $query = Translation::query()
            ->select('translations.*')
            ->join('languages', 'translations.language_id', '=', 'languages.id')
            ->where('languages.code', $locale);

        if (!empty($tags)) {
            $query->join('translation_tag', 'translations.id', '=', 'translation_tag.translation_id')
                ->join('tags', 'translation_tag.tag_id', '=', 'tags.id')
                ->whereIn('tags.name', $tags);
        }

        return $query->with('tags')
            ->paginate(self::PER_PAGE, ['*'], 'page', $page);
    }

    public function getTranslationsForExport(string $locale, array $tags = []): array
    {
        $cacheKey = "translations-export:{$locale}:" . implode(',', $tags);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($locale, $tags) {
            $query = Translation::query()
                ->select('translations.key', 'translations.value')
                ->join('languages', 'translations.language_id', '=', 'languages.id')
                ->where('languages.code', $locale);

            if (!empty($tags)) {
                $query->join('translation_tag', 'translations.id', '=', 'translation_tag.translation_id')
                    ->join('tags', 'translation_tag.tag_id', '=', 'tags.id')
                    ->whereIn('tags.name', $tags);
            }

            return $query->pluck('value', 'key')->toArray();
        });
    }

    public function createTranslation(array $data): Translation
    {
        DB::beginTransaction();
        try {
            $translation = Translation::create([
                'key' => $data['key'],
                'value' => $data['value'],
                'language_id' => $data['language_id'],
            ]);

            if (!empty($data['tags'])) {
                $tags = collect($data['tags'])->map(function ($tagName) {
                    return Tag::firstOrCreate(['name' => $tagName]);
                });

                $translation->tags()->sync($tags->pluck('id'));
            }

            DB::commit();
            $this->clearCache($translation->language->code);
            
            return $translation;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function clearCache(string $locale): void
    {
        Cache::tags(['translations', "locale:{$locale}"])->flush();
    }

    public function searchTranslations(string $query, string $locale = null, array $tags = []): LengthAwarePaginator
    {
        $translationQuery = Translation::query()
            ->select('translations.*')
            ->join('languages', 'translations.language_id', '=', 'languages.id')
            ->where(function ($q) use ($query) {
                $q->where('translations.key', 'LIKE', "%{$query}%")
                  ->orWhere('translations.value', 'LIKE', "%{$query}%");
            });

        if ($locale) {
            $translationQuery->where('languages.code', $locale);
        }

        if (!empty($tags)) {
            $translationQuery->join('translation_tag', 'translations.id', '=', 'translation_tag.translation_id')
                ->join('tags', 'translation_tag.tag_id', '=', 'tags.id')
                ->whereIn('tags.name', $tags);
        }

        return $translationQuery->with('tags')
            ->paginate(self::PER_PAGE);
    }

    public function updateTranslation(Translation $translation, array $data): Translation
    {
        DB::beginTransaction();
        try {
            $translation->update([
                'key' => $data['key'],
                'value' => $data['value'],
                'language_id' => $data['language_id'],
            ]);

            if (isset($data['tags'])) {
                $tags = collect($data['tags'])->map(function ($tagName) {
                    return Tag::firstOrCreate(['name' => $tagName]);
                });

                $translation->tags()->sync($tags->pluck('id'));
            }

            DB::commit();
            $this->clearCache($translation->language->code);
            
            return $translation;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteTranslation(Translation $translation): void
    {
        DB::beginTransaction();
        try {
            $locale = $translation->language->code;
            $translation->delete();
            
            DB::commit();
            $this->clearCache($locale);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
} 