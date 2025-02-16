<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    /**
     * @var array
     */
    private static array $usedCodes = [];

    public function definition(): array
    {
        $availableCodes = [
            'en' => 'English',
            'fr' => 'French',
            'es' => 'Spanish',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'nl' => 'Dutch',
            'ru' => 'Russian',
            'zh' => 'Chinese',
            'ja' => 'Japanese'
        ];

        // Filter out already used codes
        $availableCodes = array_diff_key($availableCodes, array_flip(self::$usedCodes));

        // If all codes are used, reset the array
        if (empty($availableCodes)) {
            self::$usedCodes = [];
            $availableCodes = [
                'en' => 'English',
                'fr' => 'French',
                'es' => 'Spanish',
                'de' => 'German',
                'it' => 'Italian'
            ];
        }

        // Get a random code and name
        $code = array_rand($availableCodes);
        $name = $availableCodes[$code];

        // Add to used codes
        self::$usedCodes[] = $code;

        return [
            'code' => $code,
            'name' => $name,
            'is_active' => true,
        ];
    }

    /**
     * Configure the model factory to use a specific language code.
     */
    public function withCode(string $code, string $name = null): self
    {
        return $this->state(function (array $attributes) use ($code, $name) {
            return [
                'code' => $code,
                'name' => $name ?? ucfirst($code),
            ];
        });
    }
} 