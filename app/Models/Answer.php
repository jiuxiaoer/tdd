<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Answer
 *
 * @property int $id
 * @property int $user_id
 * @property int $question_id
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\AnswerFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Answer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Answer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereUserId($value)
 * @mixin \Eloquent
 * @property-read mixed $down_votes_count
 * @property-read mixed $up_votes_count
 * @property-read \App\Models\User|null $owner
 * @property-read \App\Models\Question|null $question
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 */
class Answer extends Model
{
    use HasFactory;
    use Traits\VoteTrait;
    use Traits\CommentTrait;

    protected $table = 'answers';
    protected $guarded = ['id'];
    protected $appends = [
        'upVotesCount',
        'downVotesCount',
        'commentsCount',
        'commentEndpoint',
    ];
    public function question() {
        return $this->belongsTo(Question::class);
    }

    public function isBest() {
        return $this->id == $this->question->best_answer_id;
    }

    public function owner() {
        return $this->belongsTo(User::class, 'user_id');
    }
    protected static function boot()
    {
        parent::boot(); //

        static::created(function ($reply) {
            $reply->question->increment('answers_count');
        });
    }


}
