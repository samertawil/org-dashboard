<?php

namespace App\Livewire\OrgApp\EducationalActivitySchedules;

use App\Models\ActivitySchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use App\Concerns\EducationalActivitySchedule\FormTrait;

class Create extends Component
{
    use FormTrait;

    /**
     * Called once when the component is first loaded.
     * If ?copy_from=ID is present in the URL, pre-fill all form fields
     * from that existing schedule so the user only needs to adjust & save.
     */
    public function mount(): void
    {
        session(['eas_is_returning' => true]);
        $copyFromId = request()->query('copy_from');

        if ($copyFromId) {
            $source = ActivitySchedule::find($copyFromId);

            if ($source) {
                $this->group_id                    = $source->group_id ?? '';
                $this->educational_activity_domain = $source->educational_activity_domain ?? '';
                $this->target_category             = $source->target_category ?? '';
                $this->activity_name               = $source->getRawOriginal('activity_name') ?? '';
                $this->activity_description        = $source->activity_description ?? '';
                $this->educational_period_groups   = $source->educational_period_groups ?? '';
                $this->notes                       = $source->notes ?? '';
                $this->sort_order                  = $source->sort_order ?? 0;
                $this->activation                  = $source->activation ?? 1;
                $this->employee_id                 = $source->employee_id ?? '';

                // Keep the same time-of-day but clear the date so user picks a new date
                if ($source->period_start) {
                    $this->period_start = $source->period_start->format('Y-m-d\TH:i');
                }
                if ($source->period_end) {
                    $this->period_end = $source->period_end->format('Y-m-d\TH:i');
                }

                // حفظ حالة الفلاتر والتفاصيل لفتح الكولابس المناسب عند العودة
                $this->saveStateToSession($source->period_start);
            }
        }
    }

    public function save()
    {
        $this->validate();

        // فحص اذا كان يوجد حضور وغياب لليوم المراد عمل جدوله له
        if (!$this->checkAttendanceSchedule()) {
            return;
        }

        // فحص تكرار الجدول
        if (!$this->checkDuplicateSchedule()) {
            return;
        }

        ActivitySchedule::create([
            'group_id'                    => $this->group_id ?: null,
            'educational_activity_domain' => $this->educational_activity_domain ?: null,
            'target_category'             => $this->target_category ?: null,
            'activity_name'               => $this->activity_name,
            'activity_description'        => $this->activity_description ?: null,
            'period_start'                => $this->period_start,
            'period_end'                  => $this->period_end,
            'educational_period_groups'   => $this->educational_period_groups ?: null,
            'notes'                       => $this->notes ?: null,
            'sort_order'                  => (int) ($this->sort_order ?? 0),
            'activation'                  => $this->activation,
            'employee_id'                 => $this->employee_id ?: null,
            'created_by'                  => Auth::id(),
            'updated_by'                  => Auth::id(),
        ]);

        session()->flash('message', __('Schedule successfully created.'));
        $this->dispatch('scroll-to-top');

        return $this->backToEducationalActivitySchedules();
    }

    public function render()
    {

        Gate::authorize('create', ActivitySchedule::class);

        $isCopy  = (bool) request()->query('copy_from');
        $heading = $isCopy
            ? __('Copy Educational Activity Schedule')
            : __('Create Educational Activity Schedule');

        return view('livewire.org-app.educational-activity-schedules.create', [
            'heading' => $heading,
            'type'    => 'save',
        ]);
    }
}
