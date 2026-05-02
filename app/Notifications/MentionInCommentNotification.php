<?php

namespace App\Notifications;

use App\Models\Activity;
use App\Models\ActivityComments;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MentionInCommentNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Activity $activity,
        public ActivityComments $comment,
        public User $mentionedBy
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'          => 'mention',
            'activity_id'   => $this->activity->id,
            'activity_name' => $this->activity->name,
            'comment_id'    => $this->comment->id,
            'comment_text'  => $this->comment->comment,
            'mentioned_by'  => $this->mentionedBy->name,
            'url'           => route('activity.show', $this->activity->id),
        ];
    }
}
