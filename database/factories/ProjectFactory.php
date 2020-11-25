<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'manager_id' => User::factory(),
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            // TODO find how to connect developers
            'due_on' => $this->faker->dateTimeThisMonth,
        ];
    }
}
