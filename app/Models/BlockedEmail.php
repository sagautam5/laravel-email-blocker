<?php

namespace Sagautam5\EmailBlocker\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Sagautam5\EmailBlocker\Database\Factories\BlockedEmailFactory;
use Sagautam5\EmailBlocker\Enums\ReceiverType;

/**
 * Class BlockedEmail
 *
 * @property int $id
 * @property string|null $mailable
 * @property string|null $subject
 * @property string $from_name
 * @property string $from_address
 * @property string $email
 * @property string|null $content
 * @property string|null $rule
 * @property string|null $reason
 * @property ReceiverType $receiver_type
 * @property Carbon $blocked_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class BlockedEmail extends Model
{
    /** @use HasFactory<BlockedEmailFactory> * */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mailable',
        'subject',
        'from_name',
        'from_email',
        'email',
        'content',
        'rule',
        'reason',
        'receiver_type',
        'blocked_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'blocked_at' => 'datetime',
        'receiver_type' => ReceiverType::class,
    ];

    /**
     * Receiver type constants.
     */
    public const RECEIVER_TO = 'to';

    public const RECEIVER_CC = 'cc';

    public const RECEIVER_BCC = 'bcc';

    /**
     * Scope: filter by email.
     *
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    public function scopeForEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    public function getTable()
    {
        return config('email-blocker.log_table', 'blocked_emails');
    }

    /**
     * Scope: filter by rule.
     *
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    public function scopeForRule(Builder $query, string $rule): Builder
    {
        return $query->where('rule', $rule);
    }

    /**
     * Scope: only blocked by a given mailable.
     *
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    public function scopeForMailable(Builder $query, string $mailable): Builder
    {
        return $query->where('mailable', $mailable);
    }
}
