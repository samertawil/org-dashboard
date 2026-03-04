<?php

namespace App\Livewire\AppSetting\Setting;

use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;


class SettingCreate extends Component
{

    #[Validate(['required', 'unique:settings,key'])]
    public string $key;
    #[Validate(['required'])]
    public mixed $value=0;
    public string|null $description=null;
    public string|null $notes=null;
     public string $moduleName;
    public mixed $value_array=[];
    public mixed $all_templates_attchments = [];
    public mixed $attributeValue_attchments = [''];

    public function store(): void
    {
        $this->validate();
        
        $data = [];
        if (! empty($this->attributeValue_attchments[0])) {
            foreach ($this->attributeValue_attchments as $val) {
                if (!empty($val)) {
                    $data[] = $val;
                }
            }
        }

        Setting::create([
            'key' => $this->key,
            'value' => $this->value,
            'value_array' => !empty($data) ? $data : null,
            'description' => $this->description,
            'notes' => $this->notes,
        ]);

     session()->flash('message', __('Setting is Created'));

        $this->reset(['key', 'value', 'description', 'notes',  'attributeValue_attchments']);
        $this->attributeValue_attchments = [''];
    }



    public function addQuestion(): void
    {
         // @phpstan-ignore-next-line
        $this->attributeValue[] = '';
    }



    public function addQuestion_attchments(): void
    {
       
        $this->attributeValue_attchments[] = '';
    }

    public function removeQuestion_attchments(int $index): void
    {
        unset($this->attributeValue_attchments[$index]);
        $this->attributeValue_attchments = array_values($this->attributeValue_attchments);
    }




    public function render(): View
    {
        if (Gate::denies('setting.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        
        $pageTitle = __('setting');

        $settings = Setting::get();

        return view('livewire.app-setting.setting.setting-create', compact('settings'))->layoutData(['pageTitle' => $pageTitle, 'Title' => $pageTitle]);
    }
}
