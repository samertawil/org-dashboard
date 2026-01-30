<?php

namespace App\Livewire\AppSetting\Ability;

use App\Models\Role;
use App\Models\Ability;
use Livewire\Component;
use App\Models\ModuleName;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class Index extends Component
{
    use WithPagination; 
    
    // Search properties
    public string $search = '';
    public string $searchModuleName = '';
    
    // Pagination
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'searchModuleName' => ['except' => ''],
        
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingsearchModuleName(): void
    {
        $this->resetPage();
    }


    #[Computed()]
    public function abilities(): LengthAwarePaginator
    {
        return Ability::with(['module_name'])
            ->SearchName($this->search)
            ->searchModuleId($this->searchModuleName)
            ->paginate($this->perPage);
    }

    
    #[Computed]
    public function ModuleNames(): Collection
    {
        return ModuleName::get();
    }

    public function destroy(int $id): void
    {

        $abilities = Ability::find($id);

        if ($abilities) {
            $roles = Role::select('abilities', 'id', 'abilities_description')->where('abilities', 'like', "%$abilities->ability_name%")->get();

            foreach ($roles as $key => $ability) {

                $role = Role::find($ability->id);

                if($role) {

               
                // ,""
                // حذف صلاحية من المجموعة بناء على حذف هذه الصلاحية من جدول الصلاحيات 
                // اولا حذف الصلاحية كاسم فعلي للصلاحية
                $x2 = implode(",", $role->abilities);
                $x3 = str_replace($abilities->ability_name, '', $x2);
                $x4 = explode(",", $x3);

                // ثانيا حذف اسم الصلاحية اللي بالعربي abilities_description
                $x5 = implode(",", ($role->abilities_description));
                $x6 = str_replace($abilities->ability_description, '', $x5);
                $x7 = explode(",", $x6);


                $role->update([
                    'abilities' => $x4,
                    'abilities_description' => $x7,
                ]);
            }
            
            }

            $abilities->delete();
        }
    }

    public function render()
    {
        
        // if (Gate::denies('ability.all.resource')) {
        //     abort(403, 'ليس لديك الصلاحية اللازمة');
        // }
        return view('livewire.app-setting.ability.index');
    }
}
