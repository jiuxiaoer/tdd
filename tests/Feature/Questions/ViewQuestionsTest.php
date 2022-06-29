<?php

namespace Tests\Feature\Questions;

use App\Models\Answer;
use App\Models\Category;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewQuestionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_questions() {
        // 0. 抛出异常
        $this->withoutExceptionHandling();

        // 1. 访问链接 questions
        $test = $this->get('/questions');

        // 2. 正常返回 200
        $test->assertStatus(200);
    }

    /** @test */
    public function user_can_view_a_single_question() {
        $category = create(Category::class);
        // 1. 创建一个问题
        $question = Question::factory()->create(['category_id' => $category->id, 'published_at' => Carbon::parse('-1 week')]);

        // 2. 访问链接
        $test = $this->get('/questions/' . $category->slug . "/" . $question->id);

        // 3. 那么应该看到问题的内容
        $test->assertStatus(200)
            ->assertSee($question->title)
            ->assertSee($question->content);
    }

    /** @test */
    public function user_can_view_a_published_question() {
        $category = create(Category::class);
        $question = Question::factory()->create([
            'published_at' => Carbon::parse('-1 week'),
            'category_id'  => $category->id
        ]);

        $this->get('/questions/' . $category->slug . "/" . $question->id)
            ->assertStatus(200)
            ->assertSee($question->title)
            ->assertSee($question->content);
    }

    /** @test */
    public function user_cannot_view_unpublished_question() {
        $question = Question::factory()->create(['published_at' => null]);

        $this->withExceptionHandling()
            ->get('/questions/' . $question->id)
            ->assertStatus(404);
    }

    /** @test */
    public function can_see_answers_when_view_a_published_question() {
        $category = create(Category::class);
        $question = create(Question::class, [
            'published_at' => Carbon::now(),
            'category_id'  => $category->id
        ]);
        create(Answer::class, ['question_id' => $question->id], 40);

        $response = $this->get("/questions/$category->slug/$question->id");

        $result = $response->data('answers')->toArray();

        $this->assertCount(20, $result['data']);
        $this->assertEquals(40, $result['total']);
    }
}
