<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use ShabuShabu\ParadeDB\Tests\App\Models\Team;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->paragraphs(asText: true),
            'is_vip' => $this->faker->boolean(),
            'max_members' => $this->faker->randomDigit(),
            'options' => null,
            'embedding' => null,
        ];
    }

    public function maxMembers(int $members): static
    {
        return $this->state(fn (array $attributes) => [
            'max_members' => $members,
        ]);
    }

    public function isVip(bool $is = true): static
    {
        return $this->state(fn (array $attributes) => [
            'is_vip' => $is,
        ]);
    }

    public function withOptions(array $options): static
    {
        return $this->state(fn (array $attributes) => [
            'options' => $options,
        ]);
    }

    public function withEmbedding(array $vectors): static
    {
        return $this->state(fn (array $attributes) => [
            'embedding' => json_encode($vectors, JSON_THROW_ON_ERROR),
        ]);
    }
}
