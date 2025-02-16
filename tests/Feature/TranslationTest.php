<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_can_create_translation(): void
    {
        $language = Language::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/translations', [
                'key' => 'test.key',
                'value' => 'Test Value',
                'language_id' => $language->id,
                'tags' => ['web', 'mobile']
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'key',
                    'value',
                    'tags'
                ],
                'message'
            ]);
    }

    public function test_can_export_translations(): void
    {
        $language = Language::factory()->create(['code' => 'en']);
        Translation::factory()->count(10)->create(['language_id' => $language->id]);

        $response = $this->getJson('/api/translations/export?locale=en');

        $response->assertStatus(200)
            ->assertJsonCount(10);
    }
} 