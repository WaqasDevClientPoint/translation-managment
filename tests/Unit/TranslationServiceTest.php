<?php

namespace Tests\Unit;

use App\Models\Language;
use App\Models\Translation;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    private TranslationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TranslationService::class);
    }

    public function test_cache_is_cleared_after_translation_update(): void
    {
        $language = Language::factory()->create(['code' => 'en']);
        $translation = Translation::factory()->create([
            'language_id' => $language->id
        ]);

        $this->service->updateTranslation($translation, [
            'key' => 'new.key',
            'value' => 'new value',
            'language_id' => $language->id
        ]);

        // Verify cache is cleared
        $this->assertNull(Cache::get("translations:{$language->code}"));
    }

    // Add more unit tests...
} 