<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Roles>
 */
class RolesFactory extends Factory
{
    public function definition(): array
    {
        $roles = ['admin', 'medical_staff', 'inventory_officer'];

        return [
            'name' => $this->faker->unique()->randomElement($roles),
        ];
    }
}
