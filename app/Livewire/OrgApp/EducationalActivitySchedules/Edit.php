<?php

namespace App\Livewire\OrgApp\EducationalActivitySchedules;

use App\Models\ActivitySchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use App\Concerns\EducationalActivitySchedule\FormTrait;

class Edit extends Component
{
    use FormTrait;

    public ActivitySchedule $schedule;

    public function mount(ActivitySchedule $schedule)
    {
        $this->schedule = $schedule;

        // تحميل بيانات السجل في الحقول

        $this->group_id                    = $schedule->group_id;
        $this->educational_activity_domain = $schedule->educational_activity_domain;
        $this->target_category             = $schedule->target_category;
        $this->activity_name               = $schedule->getRawOriginal('activity_name');
        $this->activity_description        = $schedule->activity_description;
        $this->period_start                = $schedule->period_start?->format('Y-m-d\TH:i');
        $this->period_end                  = $schedule->period_end?->format('Y-m-d\TH:i');
        $this->educational_period_groups   = $schedule->educational_period_groups;
        $this->notes                       = $schedule->notes;
        $this->sort_order                  = $schedule->sort_order;
        $this->activation                  = $schedule->activation;
        $this->employee_id                 = $schedule->employee_id;

        // حفظ حالة الفلاتر والتفاصيل لفتح الكولابس المناسب عند العودة
        $this->saveStateToSession($schedule->period_start);
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

        $this->schedule->fill([

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
            'updated_by'                  => Auth::id(),
        ]);

        if ($this->schedule->isDirty()) {
            $this->schedule->save();
            session()->flash('message', __('Schedule successfully updated.'));
        } else {
            session()->flash('message', __('No changes were made!'));
            session()->flash('type', 'warning');
        }
        $this->dispatch('scroll-to-top');

        return $this->backToEducationalActivitySchedules();
    }

    public function render()
    {
        Gate::authorize('update', $this->schedule);

        return view('livewire.org-app.educational-activity-schedules.edit', [
            'heading' => __('Edit Educational Activity Schedule'),
            'type'    => 'save',
        ]);
    }
}
