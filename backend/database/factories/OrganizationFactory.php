<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory\u003c\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = \App\Models\Organization::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name . '-' . fake()->unique()->numberBetween(1, 9999)),
            'domain' => fake()->domainName(),
            'plan' => fake()->randomElement(['free', 'pro', 'enterprise']),
            'settings' => null,
        ];
    }
}
