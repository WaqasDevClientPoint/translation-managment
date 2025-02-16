<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_endpoints_respond_within_200ms(): void
    {
        // Setup test data
        Artisan::call('translations:generate', ['count' => 1000]);
        $token = $this->getTestToken();

        $endpoints = [
            ['method' => 'GET', 'url' => '/api/translations'],
            ['method' => 'GET', 'url' => '/api/translations/search?query=test'],
            ['method' => 'GET', 'url' => '/api/translations/export'],
        ];

        foreach ($endpoints as $endpoint) {
            $startTime = microtime(true);
            
            $response = $this->withHeader('Authorization', "Bearer {$token}")
                ->json($endpoint['method'], $endpoint['url']);
            
            $executionTime = (microtime(true) - $startTime) * 1000;
            
            $this->assertLessThan(
                200, 
                $executionTime, 
                "Endpoint {$endpoint['url']} took longer than 200ms ({$executionTime}ms)"
            );
        }
    }

    private function getTestToken(): string
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        return $user->createToken('test-token')->plainTextToken;
    }
}