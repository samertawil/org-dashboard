<?php

namespace App\Livewire\AppSetting\Role;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use App\Models\RoleUser;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class GrantUserRole extends Component
{

  public int|null $userID=null;
  public string|null $email='';
  /**
 * @var int[] or string[]
 */
  public array $rolesId = [];



  public function mount(int $userID): void
  {
    $data = RoleUser::select('role_id')->where('user_id', $userID)->get();

    $user = User::find($userID);

    if($user) {
     
      $this->email =  $user->email;
  
    }
    
    $role = [];
    foreach ($data as $myRole) {
      $role[] = $myRole->role_id;
    }

    $this->rolesId = $role;
  }

  public function store(): void
  {

    if (!empty($this->rolesId)) {

      foreach ($this->rolesId as $role) {

        RoleUser::upsert([
          [
            'user_id' => $this->userID,
            'role_id' => $role,
            'granted_by' => Auth::id()
          ],
        ], uniqueBy: ['user_id', 'role_id'],);
      }
    }


    if (empty($this->rolesId)) {

      $data = RoleUser::where('user_id', $this->userID);
      $data->Delete();
    } else {

      $data = RoleUser::wherenotin('role_id', $this->rolesId)->where('user_id', $this->userID);
      $data->Delete();
    }
    session()->flash('message', 'Apply Role successfully.');
    $this->redirectRoute('user.index');
  }




  public function render(): View
  {
    if (Gate::denies('grant.role.user')) {
      abort(403, 'You do not have the necessary permissions');
  }
    $pageTitle = __('customTrans.user role');

    $roles_group = Role::get();

    return view('livewire.app-setting.role.grant-user-role', compact('roles_group'))->layoutData(['pageTitle' => $pageTitle, 'title' => $pageTitle]);
  }
}
