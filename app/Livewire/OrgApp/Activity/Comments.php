<?php

namespace App\Livewire\OrgApp\Activity;

use App\Enums\GlobalSystemConstant;
use App\Models\Activity;
use App\Models\ActivityComments;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\MentionInCommentNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Comments extends Component
{
    public Activity $activity;
    public string $newComment = '';

    /** Populated once in mount() — passed directly to Alpine for @mention */
    public array $mentionableUsers = [];

    public function mount(Activity $activity): void
    {
        $this->activity = $activity;
        $this->mentionableUsers = Employee::query()
            ->whereNotNull('user_id')
            ->where('activation', GlobalSystemConstant::ACTIVE->value)
            ->where('user_id', '!=', Auth::id())
            ->with('user:id,name')
            ->get()
            ->filter(fn($e) => $e->user !== null)
            ->map(fn($e) => [
                'id'   => $e->user->id,
                'name' => $e->full_name ?? $e->user->name,
            ])
            ->values()
            ->toArray();
    }

    /** All comments for this activity */
    #[Computed]
    public function comments()
    {
        return $this->activity
            ->comments()
            ->with('creator:id,name,avatar,google_id')
            ->get();
    }

    public function addComment(): void
    {
        $this->validate(['newComment' => 'required|string|min:1|max:1000']);

        $comment = ActivityComments::create([
            'activity_id' => $this->activity->id,
            'comment'     => $this->newComment,
            'created_by'  => Auth::id(),
        ]);

        // Extract @mentions and notify
        $this->processMentions($comment);

        $this->newComment = '';
        unset($this->comments); // reset computed
    }

    private function processMentions(ActivityComments $comment): void
    {
        $text = $comment->comment;

        // 1. Get all potential mentionable names from the database
        $employeeNames = Employee::whereNotNull('user_id')
            ->where('activation', GlobalSystemConstant::ACTIVE->value)
            ->pluck('full_name')
            ->filter()
            ->toArray();

        $userNames = User::pluck('name')
            ->filter()
            ->toArray();

        $allPossibleNames = array_unique(array_merge($employeeNames, $userNames));

        // 2. Sort names by length descending (longest first) to correctly handle names with spaces
        // and avoid partial matches (e.g., matching "@Samer" inside "@Samer Al-Tawil")
        usort($allPossibleNames, function($a, $b) {
            return mb_strlen($b) <=> mb_strlen($a);
        });

        $notifiedUserIds = [];

        foreach ($allPossibleNames as $name) {
            $mention = '@' . $name;

            if (mb_strpos($text, $mention) !== false) {
                // Find associated User IDs
                $userIds = Employee::where('full_name', $name)
                    ->whereNotNull('user_id')
                    ->pluck('user_id')
                    ->toArray();

                if (empty($userIds)) {
                    $userIds = User::where('name', $name)->pluck('id')->toArray();
                }

                foreach ($userIds as $userId) {
                    if ($userId && $userId != Auth::id() && !in_array($userId, $notifiedUserIds)) {
                        $user = User::find($userId);
                        if ($user) {
                            $user->notify(new MentionInCommentNotification(
                                $this->activity,
                                $comment,
                                Auth::user()
                            ));
                            $notifiedUserIds[] = $userId;
                        }
                    }
                }

                // Replace the found mention with a placeholder to avoid re-matching
                $text = str_replace($mention, ' ___MENTIONED___ ', $text);
            }
        }
    }

    public function deleteComment(int $id): void
    {
        $comment = ActivityComments::find($id);

        if ($comment && $comment->created_by === Auth::id()) {
            $comment->delete();
            unset($this->comments);
        }
    }

    public function render()
    {
        return view('livewire.org-app.activity.comments');
    }
}
