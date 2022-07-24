<?php

namespace App\Models;

use App\Notifications\QuestionWasUpdated;
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
 * @property int|null $best_answer_id
 * @property int $category_id
 * @property string|null $slug
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\User|null $creator
 * @property-read mixed $down_votes_count
 * @property-read int|null $subscriptions_count
 * @property-read mixed $up_votes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription[] $subscriptions
 * @method static \Illuminate\Database\Eloquent\Builder|Question drafts($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|Question filter($filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Question published()
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereAnswersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereBestAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereSlug($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 */
class Question extends Model
{
    use HasFactory;
    use Traits\VoteTrait;
    use Traits\CommentTrait;
    use Traits\InvitedUsersTrait;

    protected $table = 'questions';
    // 这里也放开了属性保护
    protected $guarded = ['id'];
    protected $with = ['category'];
    protected $appends = [
        'upVotesCount',
        'downVotesCount',
        'subscriptionsCount',
        'commentsCount',
        'commentEndpoint',
    ];

    public function getSubscriptionsCountAttribute()
    {
        return $this->subscriptions->count();
    }
    public function scopePublished($query) {
        return $query->whereNotNull('published_at');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
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
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
    public function creator() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function publish() {
        $this->update([
            'published_at' => Carbon::now()
        ]);
    }

    public function scopeDrafts($query, $userId)
    {
        return $query->where(['user_id' => $userId])->whereNull('published_at');
    }

    public function subscribe($userId)
    {
        $this->subscriptions()->create([
            'user_id' => $userId
        ]);

        return $this;
    }
    public function unsubscribe($userId)
    {
        $this->subscriptions()
            ->where('user_id', $userId)
            ->delete();

        return $this;
    }

    public function addAnswer($answer)
    {
        $answer = $this->answers()->create($answer);

        $this->subscriptions
            ->where('user_id', '!=', $answer->user_id)
            ->each
            ->notify($answer);

        return $answer;
    }
    public function path()
    {
        return $this->slug ? "/questions/{$this->category->slug}/{$this->id}/{$this->slug}" : "/questions/{$this->category->slug}/{$this->id}";
    }
    public function isSubscribedTo($user)
    {
        if (! $user) {
            return false;
        }

        return $this->subscriptions()
            ->where('user_id', $user->id)
            ->exists();
    }

}
