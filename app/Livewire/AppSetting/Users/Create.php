<?php

namespace App\Livewire\AppSetting\Users;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Create extends Component
{
    public string $name='';
    public string $email='';
    public $password='';
    public string $password_confirmation = '';

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|email:rfc,dns',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

       User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'password_confirmation' => $this->password_confirmation,
        ]);

        session()->flash('message', 'User created successfully.');

       
        $this->reset(['name', 'email', 'password', 'password_confirmation']);
    }
    public function render()
    {
        if (Gate::denies('user.create')) {
            abort(403, 'You do not have the necessary permissions');
        }
        return view('livewire.app-setting.users.create');
    }
}
