<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Question
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property string|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Answer[] $answers
 * @property-read int|null $answers_count
 * @method static \Database\Factories\QuestionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereUserId($value)
 * @mixin \Eloquent
 */
class Question extends Model
{
    use HasFactory;

    // 这里也放开了属性保护
    protected $guarded = ['id'];

    public function scopePublished($query) {
        return $query->whereNotNull('published_at');
    }
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }
    public function answers() {
        return $this->hasMany(Answer::class);
    }

    public function markAsBestAnswer($answer) {
        $this->update([
            'best_answer_id' => $answer->id
        ]);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function publish() {
        $this->update([
            'published_at' => Carbon::now()
        ]);
    }

    public function invitedUsers() {
        preg_match_all('/@([^\s.]+)/', $this->content, $matches);

        return $matches[1];
    }


    public function scopeDrafts($query, $userId)
    {
        return $query->where(['user_id' => $userId])->whereNull('published_at');
    }
}
