<?php

namespace App\Livewire\OrgApp\Activity;

use App\Enums\GlobalSystemConstant;
use App\Models\Activity;
use App\Models\ActivityComments;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\MentionInCommentNotification;
use App\Reposotries\employeeRepo;
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
        $this->mentionableUsers = employeeRepo::mentionEmp();
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
        $notifiedUserIds = [];

        // Sort by length descending to match full names before partial names
        $employees = collect($this->mentionableUsers)->sortByDesc(fn($e) => mb_strlen($e['name']));

        foreach ($employees as $employee) {
            $name = $employee['name'];
            $mention = '@' . $name;

            if (mb_strpos($text, $mention) !== false || mb_strpos($text, $name) !== false) {
                $userId = $employee['id'];

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
