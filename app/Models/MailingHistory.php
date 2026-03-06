<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property array $subject {"de": "...", "hu": "..."}
 * @property array $message {"de": "...", "hu": "..."}
 * @property string|null $url
 * @property array|null $url_label {"de": "...", "hu": "..."}
 * @property array|null $attachments [{"locale": "de", "original": "file.pdf"}, ...]
 * @property bool $include_mailing_list
 * @property bool $set_link
 * @property bool $set_attachment
 * @property bool $set_personal_greeting
 * @property int $recipient_count
 * @property int $member_count
 * @property int $mailing_list_count
 * @property Carbon $created_at = sent_at
 * @property Carbon|null $updated_at
 */
final class MailingHistory extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'url',
        'url_label',
        'attachments',
        'include_mailing_list',
        'set_link',
        'set_attachment',
        'set_personal_greeting',
        'recipient_count',
        'member_count',
        'mailing_list_count',
    ];

    protected $casts = [
        'subject' => 'array',
        'message' => 'array',
        'url_label' => 'array',
        'attachments' => 'array',
        'include_mailing_list' => 'boolean',
        'set_link' => 'boolean',
        'set_attachment' => 'boolean',
        'set_personal_greeting' => 'boolean',
        'recipient_count' => 'integer',
        'member_count' => 'integer',
        'mailing_list_count' => 'integer',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Convenience: subject for a given locale with fallback */
    public function subjectFor(string $locale): string
    {
        return $this->subject[$locale] ?? collect($this->subject)->first() ?? '';
    }

    /** Convenience: message preview (first 200 chars) for a given locale */
    public function previewFor(string $locale): string
    {
        $text = $this->message[$locale] ?? collect($this->message)->first() ?? '';

        return mb_strlen($text) > 200 ? mb_substr($text, 0, 200).'…' : $text;
    }
}
