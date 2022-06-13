<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostAnswersTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function user_can_post_an_answer_to_a_question() {
        // 假设已存在某个问题 创建问题创建用户
        $question = Question::factory()->create();
        $user     = User::factory()->create();
        // 然后我们触发某个路由 回答问题 http 请求
        $response = $this->post("/questions/{$question->id}/answers", [
            'user_id' => $user->id,
            'content' => 'This is an answer.'
        ]);
        // 我们要看到预期结果
        $response->assertStatus(201);

        $answer = $question->answers()->where('user_id', $user->id)->first();
        $this->assertNotNull($answer);

        $this->assertEquals(1, $question->answers()->count());
    }
}
