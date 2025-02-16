<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationSearchTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;
    private Language $englishLanguage;
    private Language $frenchLanguage;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user and token
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
        
        // Create languages once for all tests
        $this->englishLanguage = Language::factory()->withCode('en', 'English')->create();
        $this->frenchLanguage = Language::factory()->withCode('fr', 'French')->create();
    }

    public function test_can_search_translations_by_key(): void
    {
        Translation::factory()
            ->withLanguage($this->englishLanguage)
            ->create([
                'key' => 'welcome.message',
                'value' => 'Hello World',
            ]);

        Translation::factory()
            ->withLanguage($this->englishLanguage)
            ->create([
                'key' => 'goodbye.message',
                'value' => 'Goodbye World',
            ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/translations/search?query=welcome');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', 'welcome.message');
    }

    public function test_can_search_translations_by_value(): void
    {
        Translation::factory()
            ->withLanguage($this->englishLanguage)
            ->create([
                'key' => 'test.key1',
                'value' => 'Search this text',
            ]);

        Translation::factory()
            ->withLanguage($this->englishLanguage)
            ->create([
                'key' => 'test.key2',
                'value' => 'Different text',
            ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/translations/search?query=Search');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.value', 'Search this text');
    }

    public function test_can_search_translations_with_tags(): void
    {
        $tag = Tag::factory()->create(['name' => 'mobile']);
        
        $translation = Translation::factory()
            ->withLanguage($this->englishLanguage)
            ->create([
                'key' => 'mobile.welcome',
                'value' => 'Welcome to mobile app',
            ]);
        
        $translation->tags()->attach($tag);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/translations/search?query=welcome&tags[]=mobile');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', 'mobile.welcome');
    }

    public function test_search_respects_locale_filter(): void
    {
        Translation::factory()
            ->withLanguage($this->englishLanguage)
            ->create([
                'key' => 'welcome',
                'value' => 'Hello',
            ]);

        Translation::factory()
            ->withLanguage($this->frenchLanguage)
            ->create([
                'key' => 'welcome',
                'value' => 'Bonjour',
            ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/translations/search?query=welcome&locale=fr');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.value', 'Bonjour');
    }

    public function test_search_validates_minimum_query_length(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/translations/search?query=a');

        $response->assertStatus(422);
    }
} 