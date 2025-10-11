<?php

namespace Database\Factories;

use App\Models\Roles;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RoleFactory extends Factory
{
    protected $model = Roles::class;

    public function definition(): array
    {
        $roles = ['admin', 'medical_staff', 'inventory_officer'];

        return [
            'name' => $this->faker->unique()->randomElement($roles),
        ];
    }
}
