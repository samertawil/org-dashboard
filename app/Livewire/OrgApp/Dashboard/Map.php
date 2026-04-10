<?php
namespace App\Livewire\OrgApp\Dashboard;

use Livewire\Component;
use App\Models\AcademicCycle; // افترض جدول الفصول

class  Map extends Component
{
    public $currentCycle; // الفصل الحالي

    // public function mount()
    // {
    //     $this->currentCycle = AcademicCycle::current()->first();
    // }

    public function getStepsProperty()
    {
        return [
            'curricula' => [
                'icon' => '📘',
                'title' => 'المناهج التعليمية',
               'completed' => 'completed',
                'route' => 'curricula.index'
            ],
            'learning_points' => [
                'icon' => '👥',
                'title' => 'النقط التعليمية',
               'completed' => 'completed',
                'route' => 'learning-points.index'
            ],
            'students' => [
                'icon' => '👤',
                'title' => 'بيانات الطلاب',
                'completed' => 'completed',
                'route' => 'students.index'
            ],
            'attendance' => [
                'icon' => '📅',
                'title' => 'الحضور والغياب',
               'completed' => 'completed',
                'route' => 'attendance.index'
            ],
            'evaluations' => [
                'icon' => '📝',
                'title' => 'التقييمات',
                'completed' =>'completed',
                'route' => 'evaluations.index'
            ],
        ];
    }

    public function render()
    {
        return view('livewire.org-app.dashboard.map');
    }
}