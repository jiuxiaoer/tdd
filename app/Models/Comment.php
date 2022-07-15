<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Comment
 *
 * @property int $id
 * @property int $user_id
 * @property string $content
 * @property int $commented_id
 * @property string $commented_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\CommentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUserId($value)
 * @mixin \Eloquent
 * @property-read Model|\Eloquent $commented
 * @property-read mixed $down_votes_count
 * @property-read mixed $up_votes_count
 * @property-read \App\Models\User|null $owner
 */
class Comment extends Model
{
    use HasFactory;
    use Traits\VoteTrait;
    use Traits\InvitedUsersTrait;

    protected $guarded = ['id'];
    protected $with = ['owner'];

    protected $appends = [
        'upVotesCount',
        'downVotesCount',
    ];

    public function commented()
    {
        return $this->morphTo();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
