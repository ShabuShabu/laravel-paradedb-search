<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use ShabuShabu\ParadeDB\Tests\App\Models\Team;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->paragraphs(asText: true),
            'is_vip' => $this->faker->boolean(),
            'max_members' => $this->faker->randomDigit(),
            'options' => null,
        ];
    }
}
