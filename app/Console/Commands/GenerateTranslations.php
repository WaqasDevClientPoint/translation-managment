<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateTranslations extends Command
{
    protected $signature = 'translations:generate {count=100000}';
    protected $description = 'Generate test translations';

    public function handle(): void
    {
        $count = $this->argument('count');
        $this->info("Generating {$count} translations...");

        // Create sample languages if they don't exist
        $languages = $this->createLanguages();
        
        // Create sample tags
        $tags = $this->createTags();

        $bar = $this->output->createProgressBar($count);
        
        DB::beginTransaction();
        try {
            foreach (range(1, $count) as $i) {
                $translation = Translation::create([
                    'key' => "key.sample.{$i}",
                    'value' => "Sample translation value {$i}",
                    'language_id' => $languages->random()->id,
                ]);

                // Attach random tags
                $translation->tags()->attach(
                    $tags->random(rand(1, 3))->pluck('id')->toArray()
                );

                $bar->advance();
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage());
        }

        $bar->finish();
        $this->info("\nTranslations generated successfully!");
    }

    private function createLanguages(): \Illuminate\Database\Eloquent\Collection
    {
        $languages = [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'es', 'name' => 'Spanish'],
            ['code' => 'fr', 'name' => 'French'],
        ];

        foreach ($languages as $language) {
            Language::firstOrCreate(['code' => $language['code']], $language);
        }

        return Language::all();
    }

    private function createTags(): \Illuminate\Database\Eloquent\Collection
    {
        $tags = ['mobile', 'desktop', 'web', 'ios', 'android'];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['name' => $tag]);
        }

        return Tag::all();
    }
} 