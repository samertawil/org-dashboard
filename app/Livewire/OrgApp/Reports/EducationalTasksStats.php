<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\ActivitySchedule;
use App\Models\EducationalActivityDetail;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class EducationalTasksStats extends Component
{
    /**
     * Build a batch → month → group statistics tree.
     * Structure:
     *   batch_no (string)
     *     └── month_key (Y-m)
     *           └── group_id
     *                 └── { name, total, completed, delayed, required_now, upcoming }
     */
    #[Computed]
    public function statistics(): array
    {
        // Load all active schedules with their group and activityDetail in one query
        $schedules = ActivitySchedule::with(['group', 'activityDetail'])
            ->active()
            ->get();

        $tree = [];

        foreach ($schedules as $schedule) {
            $batch     = $schedule->group?->batch_no ?? __('No Batch');
            $monthKey  = $schedule->period_start?->format('Y-m') ?? __('No Date');
            $groupId        = $schedule->group_id ?? 0;
            $groupName      = $schedule->group?->name ?? __('No Group');
            $groupShortName = $schedule->group?->short_name ?? $groupName;

            // Initialise nodes
            if (!isset($tree[$batch])) {
                $tree[$batch] = ['total' => 0, 'completed' => 0, 'happen_now' => 0, 'delayed' => 0, 'require_today' => 0, 'upcoming' => 0, 'months' => []];
            }
            if (!isset($tree[$batch]['months'][$monthKey])) {
                $tree[$batch]['months'][$monthKey] = ['total' => 0, 'completed' => 0, 'happen_now' => 0, 'delayed' => 0, 'require_today' => 0, 'upcoming' => 0, 'groups' => []];
            }
            if (!isset($tree[$batch]['months'][$monthKey]['groups'][$groupId])) {
                $tree[$batch]['months'][$monthKey]['groups'][$groupId] = ['name' => $groupName, 'short_name' => $groupShortName, 'total' => 0, 'completed' => 0, 'happen_now' => 0, 'delayed' => 0, 'require_today' => 0, 'upcoming' => 0];
            }

            $status = $schedule->task_status; // uses accessor on ActivitySchedule

            // Increment all levels
            $tree[$batch]['total']++;
            $tree[$batch][$status]++;
            $tree[$batch]['months'][$monthKey]['total']++;
            $tree[$batch]['months'][$monthKey][$status]++;
            $tree[$batch]['months'][$monthKey]['groups'][$groupId]['total']++;
            $tree[$batch]['months'][$monthKey]['groups'][$groupId][$status]++;
        }

        // Sort by batch number then by month
        ksort($tree);
        foreach ($tree as &$batchNode) {
            ksort($batchNode['months']);
        }
        unset($batchNode);

        return $tree;
    }

    #[Title('Educational Tasks Statistics')]
    public function render()
    {
        Gate::authorize('select.any.student');

        return view('livewire.org-app.reports.educational-tasks-stats', [
            'statistics' => $this->statistics,
        ]);
    }
}
