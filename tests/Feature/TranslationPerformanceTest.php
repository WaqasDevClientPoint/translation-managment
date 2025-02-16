<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranslationPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_endpoint_performance(): void
    {
        // Generate test data
        Artisan::call('translations:generate', ['count' => 100000]);

        $startTime = microtime(true);
        
        $response = $this->getJson('/api/translations/export?locale=en');
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $response->assertStatus(200);
        $this->assertLessThan(500, $executionTime, 'Export took longer than 500ms');
    }
} 