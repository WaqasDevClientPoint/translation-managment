<?php

namespace Database\Factories;

use App\Models\Translation;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->word . '.' . $this->faker->word,
            'value' => $this->faker->sentence,
            'language_id' => fn () => Language::factory(),
        ];
    }

    /**
     * Configure the model factory to use a specific language.
     */
    public function withLanguage(Language $language): self
    {
        return $this->state(function (array $attributes) use ($language) {
            return [
                'language_id' => $language->id,
            ];
        });
    }
} 