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
 */
class Answer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function question() {
        return $this->belongsTo(Question::class);
    }

    public function isBest() {
        return $this->id == $this->question->best_answer_id;
    }

    public function owner() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function voteUp($user) {
        $attributes = ['user_id' => $user->id];

        if (!$this->votes('vote_up')->where($attributes)->exists()) {
            $this->votes('vote_up')->create(['user_id' => $user->id, 'type' => 'vote_up']);
        }
    }

    public function votes($type) {
        return $this->morphMany(Vote::class, 'voted')->whereType($type);
    }

    public function cancelVoteUp($user) {
        $this->votes('vote_up')->where(['user_id' => $user->id, 'type' => 'vote_up'])->delete();
    }

    public function isVotedUp($user) {
        if (!$user) {
            return false;
        }

        return $this->votes('vote_up')->where('user_id', $user->id)->exists();
    }
    public function getUpVotesCountAttribute()
    {
        return $this->votes('vote_up')->count();
    }
    public function voteDown($user){
        $this->votes('vote_down')->create(['user_id' => $user->id, 'type' => 'vote_down']);
    }
    public function cancelVoteDown($user)
    {
        $this->votes('vote_down')->where(['user_id' => $user->id, 'type' => 'vote_down'])->delete();
    }
}
