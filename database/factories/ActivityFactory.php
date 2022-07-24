<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $question = Question::factory()->create();
        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'subject_id' => $question->id,
            'subject_type' => get_class($question),
            'type' => 'published_question'
        ];
    }
}
