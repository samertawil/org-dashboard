<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading ?? __('Users List') }}</flux:heading>
            <flux:subheading>{{ $subheading ?? __('Manage and monitor system users and their access levels.') }}</flux:subheading>
        </div>

        <span title="{{ __('Create a new system user') }}" class="w-full sm:w-auto">
            <flux:button href="{{ route('user.create') }}" wire:navigate variant="primary" icon="plus" class="w-full">
                {{ __('Create New User') }}
            </flux:button>
        </span>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Search & Filter Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-900/30">
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                <flux:icon name="magnifying-glass" class="size-4" />
                {{ __('Search & Filter Users') }}
            </h2>
        </div>
        <div class="p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Search by Name --}}
                <div class="relative">
                    <flux:input wire:model.live.debounce.300ms="search" :label="__('Search by Name')" type="text"
                        :placeholder="__('Enter name...')" icon="user" />
                    <div wire:loading wire:target="search" class="absolute right-3 top-[2.4rem]">
                        <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                    </div>
                </div>
                
                {{-- Search by Email --}}
                <div class="relative">
                    <flux:input wire:model.live.debounce.300ms="searchEmail" :label="__('Search by Email')" type="text"
                        :placeholder="__('Enter email...')" icon="envelope" />
                    <div wire:loading wire:target="searchEmail" class="absolute right-3 top-[2.4rem]">
                        <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                    </div>
                </div>

                <div class="relative">
                    <flux:select wire:model.live="searchActivation" :label="__('Status')"
                        :placeholder="__('All statuses')">
                        <option value="">{{ __('All Statuses') }}</option>
                        @foreach (\App\Enums\GlobalSystemConstant::options()->where('type', 'status') as $status)
                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                        @endforeach
                    </flux:select>
                    <div wire:loading wire:target="searchActivation" class="absolute right-8 top-[2.4rem]">
                        <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                    </div>
                </div>

                {{-- Filter by Role --}}
                <div class="relative">
                    <flux:select wire:model.live="searchRole" :label="__('Filter by Role')"
                        :placeholder="__('All roles')">
                        <option value="">{{ __('All Roles') }}</option>
                        @foreach ($this->roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </flux:select>
                    <div wire:loading wire:target="searchRole" class="absolute right-8 top-[2.4rem]">
                        <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                    </div>
                </div>
            </div>

            {{-- Clear Filters --}}
            @if ($search || $searchEmail || $searchActivation || $searchRole)
                <div class="flex items-center justify-end pt-2">
                    <span title="{{ __('Reset all search filters') }}">
                        <flux:button wire:click="$set('search', ''); $set('searchEmail', ''); $set('searchActivation', ''); $set('searchRole', '');" variant="ghost" size="sm"
                            icon="x-mark">
                            {{ __('Clear Filters') }}
                        </flux:button>
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- Data Section --}}
    @if ($users->count() > 0)
        <div class="flex flex-col gap-4">
            {{-- Table Header Info --}}
            <div class="px-4 py-2">
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Showing') }} <span class="font-medium text-zinc-900 dark:text-white">{{ $users->firstItem() }}</span>
                    {{ __('to') }} <span class="font-medium text-zinc-900 dark:text-white">{{ $users->lastItem() }}</span>
                    {{ __('of') }} <span class="font-medium text-zinc-900 dark:text-white">{{ $users->total() }}</span>
                    {{ __('users') }}
                </p>
            </div>

            {{-- Mobile Card View --}}
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @foreach ($users as $user)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-4 space-y-4">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center text-zinc-500 font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    @if ($user->employee)
                                        <button wire:click="showEmployee({{ $user->id }})" class="font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline focus:outline-none transition-colors text-left">
                                            {{ $user->name }}
                                        </button>
                                    @else
                                        <h3 class="font-semibold text-zinc-900 dark:text-white">{{ $user->name }}</h3>
                                    @endif
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div @class([
                                'px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' => $user->activation == 1,
                                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' => $user->activation == 0,
                            ])>
                                {{ data_get(\App\Enums\GlobalSystemConstant::options()->where('type', 'status')->firstWhere('value', $user->activation), 'label', __('No Status')) }}
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-700">
                            <span title="{{ __('View Roles') }}">
                                <flux:button wire:click="showUserRoles({{ $user->id }})" variant="ghost" size="sm" icon="eye" />
                            </span>
                            <span title="{{ __('Grant privileges to this user') }}">
                                <flux:button href="{{ route('grant.role.user', $user->id) }}" variant="ghost" size="sm" icon="shield-check" wire:navigate />
                            </span>
                            <span title="{{ __('Reset user password to default') }}">
                                <flux:button wire:click="resetPass({{ $user->id }})" wire:confirm="{{ __('Reset password to default?') }}" variant="ghost" size="sm" icon="key" />
                            </span>
                            <span title="{{ __('Toggle user activation status') }}">
                                <flux:button wire:click="switchActivation({{ $user->id }})" variant="ghost" size="sm" icon="arrow-path" />
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700 text-left">
                        <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider w-16">#</th>
                                <th wire:click="sortBy('name')" class="px-6 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider cursor-pointer hover:text-zinc-700 transition-colors">
                                    <div class="flex items-center gap-1">
                                        {{ __('User Name') }}
                                        <flux:icon name="{{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'chevron-up' : 'chevron-down') : 'chevron-up-down' }}" class="size-3" />
                                    </div>
                                </th>
                                <th wire:click="sortBy('email')" class="px-6 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider cursor-pointer hover:text-zinc-700 transition-colors">
                                    <div class="flex items-center gap-1">
                                        {{ __('Email Address') }}
                                        <flux:icon name="{{ $sortField === 'email' ? ($sortDirection === 'asc' ? 'chevron-up' : 'chevron-down') : 'chevron-up-down' }}" class="size-3" />
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach ($users as $index => $user)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/20 transition-colors">
                                    <td class="px-6 py-4 text-sm text-zinc-500">{{ $users->firstItem() + $index }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="size-8 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center text-xs text-zinc-500 font-bold">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            @if ($user->employee)
                                                <button wire:click="showEmployee({{ $user->id }})" class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline focus:outline-none transition-colors text-left">
                                                    {{ $user->name }}
                                                </button>
                                            @else
                                                <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $user->name }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">{{ $user->email }}</td>
                                    <td class="px-6 py-4">
                                        <div @class([
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' => $user->activation == 1,
                                            'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' => $user->activation == 0,
                                        ])>
                                            {{ data_get(\App\Enums\GlobalSystemConstant::options()->where('type', 'status')->firstWhere('value', $user->activation), 'label', __('No Status')) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span title="{{ __('View Roles') }}">
                                                <flux:button wire:click="showUserRoles({{ $user->id }})" variant="ghost" size="sm" icon="eye" />
                                            </span>
                                            <flux:dropdown>
                                                <flux:button variant="ghost" icon="cog-6-tooth" size="sm" />
                                                <flux:menu>
                                                    <flux:menu.item wire:click="showUserRoles({{ $user->id }})" icon="eye">
                                                        {{ __('View Roles') }}
                                                    </flux:menu.item>
                                                    <flux:menu.item wire:click="resetPass({{ $user->id }})" wire:confirm="{{ __('Are you sure you want to reset password to default?') }}" icon="key">
                                                        {{ __('Reset Password') }}
                                                    </flux:menu.item>
                                                    <flux:menu.item href="{{ route('grant.role.user', $user->id) }}" icon="shield-check" wire:navigate>
                                                        {{ __('Grant Privileges') }}
                                                    </flux:menu.item>
                                                    <flux:menu.item wire:click="switchActivation({{ $user->id }})" icon="arrow-path">
                                                        {{ __('Switch Activation') }}
                                                    </flux:menu.item>
                                                </flux:menu>
                                            </flux:dropdown>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-2">
                {{ $users->links() }}
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-20 px-4 bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="flex items-center justify-center w-20 h-20 rounded-full bg-zinc-100 dark:bg-zinc-700 mb-6">
                <flux:icon name="users" class="w-10 h-10 text-zinc-400 dark:text-zinc-500" />
            </div>
            <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-2">{{ __('No Users Found') }}</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 text-center mb-8 max-w-sm">
                @if ($search || $searchEmail || $searchActivation || $searchRole)
                    {{ __('No users match your current filters. Try refining your search or clearing the filters.') }}
                @else
                    {{ __('You haven\'t added any users yet. Start building your team by creating a new user.') }}
                @endif
            </p>
            @if ($search || $searchEmail || $searchActivation || $searchRole)
                <flux:button wire:click="$set('search', ''); $set('searchEmail', ''); $set('searchActivation', ''); $set('searchRole', '');" variant="primary" icon="x-mark">
                    {{ __('Clear All Filters') }}
                </flux:button>
            @else
                <flux:button href="{{ route('user.create') }}" wire:navigate variant="primary" icon="plus" class="px-8">
                    {{ __('Create First User') }}
                </flux:button>
            @endif
        </div>
    @endif

    {{-- Employee Details Modal --}}
    <flux:modal wire:model="showEmployeeModal" class="md:w-[650px]" @close="$wire.closeEmployeeModal()">
        @if ($this->selectedEmployee)
            <div class="space-y-6">
                {{-- Header / Profile section --}}
                <div class="flex flex-col sm:flex-row items-center gap-4 pb-4 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="h-16 w-16 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-2xl font-bold shrink-0">
                        {{ strtoupper(substr($this->selectedEmployee->full_name ?? $this->selectedEmployee->user->name, 0, 1)) }}
                    </div>
                    <div class="text-center sm:text-left space-y-1">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white">
                            {{ $this->selectedEmployee->full_name }}
                        </h2>
                        <div class="flex flex-wrap justify-center sm:justify-start gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-mono font-medium bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                                #{{ $this->selectedEmployee->employee_number }}
                            </span>
                            @php
                                $statusEnum = \App\Enums\GlobalSystemConstant::tryFrom($this->selectedEmployee->activation);
                            @endphp
                            @if ($statusEnum)
                                <span @class([
                                    'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                    'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' => $this->selectedEmployee->activation == 1,
                                    'bg-zinc-100 text-zinc-700 dark:bg-zinc-500/20 dark:text-zinc-400' => $this->selectedEmployee->activation != 1,
                                ])>
                                    {{ $statusEnum->label() }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Details Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    {{-- Department --}}
                    <div class="space-y-1">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">{{ __('Department') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $this->selectedEmployee->department->name ?? '-' }}
                        </span>
                    </div>

                    {{-- Partner Institution / Source --}}
                    <div class="space-y-1">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">{{ __('Partner / Institution') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $this->selectedEmployee->employee_in_partner_id ? ($this->selectedEmployee->partner->name ?? '-') : 'AFSC' }}
                        </span>
                    </div>


                    {{-- Position Status --}}
                    <div class="space-y-1">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">{{ __('Position') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $this->selectedEmployee->positionStatus->status_name ?? '-' }}
                        </span>
                    </div>

                    {{-- Hiring Type --}}
                    <div class="space-y-1">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">{{ __('Hiring Type') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $this->selectedEmployee->hiringType->status_name ?? '-' }}
                        </span>
                    </div>

                    {{-- Date of Joining --}}
                    <div class="space-y-1">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">{{ __('Date of Joining') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $this->selectedEmployee->date_of_joining ?? '-' }}
                        </span>
                    </div>

                    <div class="border-t border-zinc-100 dark:border-zinc-700/50 md:col-span-2 my-2"></div>

                    {{-- Email --}}
                    <div class="space-y-1">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">{{ __('Email') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100 break-all">
                            {{ $this->selectedEmployee->email ?? $this->selectedEmployee->user->email }}
                        </span>
                    </div>

                    {{-- Phone --}}
                    <div class="space-y-1">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">{{ __('Phone') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $this->selectedEmployee->phone ?? '-' }}
                        </span>
                    </div>

                    {{-- Gender --}}
                    <div class="space-y-1">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">{{ __('Gender') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                            @php
                                $genderEnum = \App\Enums\GlobalSystemConstant::tryFrom($this->selectedEmployee->gender);
                            @endphp
                            @if($genderEnum)
                                <span>{{ $genderEnum->icon() }}</span>
                                <span>{{ $genderEnum->label() }}</span>
                            @else
                                -
                            @endif
                        </span>
                    </div>

                    {{-- Date of Birth --}}
                    <div class="space-y-1">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">{{ __('Date of Birth') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $this->selectedEmployee->date_of_birth ?? '-' }}
                        </span>
                    </div>

                    {{-- Marital Status --}}
                    <div class="space-y-1">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">{{ __('Marital Status') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $this->selectedEmployee->maritalStatus->status_name ?? '-' }}
                        </span>
                    </div>

                    {{-- Region / Address --}}
                    <div class="space-y-1">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">{{ __('Region') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $this->selectedEmployee->region->status_name ?? '-' }}
                        </span>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="closeEmployeeModal" variant="ghost">
                        {{ __('Close') }}
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

    {{-- User Roles Details Modal --}}
    <flux:modal wire:model="showRolesModal" class="md:w-[500px]" @close="$wire.closeRolesModal()">
        @if ($this->selectedUserForRoles)
            <div class="space-y-6">
                {{-- Header section --}}
                <div class="flex flex-col sm:flex-row items-center gap-4 pb-4 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="h-12 w-12 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xl font-bold shrink-0">
                        <flux:icon name="shield-check" class="size-6" />
                    </div>
                    <div class="text-center sm:text-left space-y-1">
                        <h2 class="text-lg font-bold text-zinc-900 dark:text-white">
                            {{ __('Roles & Permissions') }}
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ __('Granted roles and permissions for user:') }} <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $this->selectedUserForRoles->name }}</span>
                        </p>
                    </div>
                </div>

                {{-- Roles list --}}
                <div class="space-y-4 max-h-[300px] overflow-y-auto pr-1">
                    @if ($this->selectedUserRoles->count() > 0)
                        <div class="flex flex-col gap-3">
                            @foreach ($this->selectedUserRoles as $role)
                                <div class="bg-zinc-50 dark:bg-zinc-900/40 rounded-xl border border-zinc-200 dark:border-zinc-700/80 p-4 space-y-2">
                                    <div class="flex justify-between items-center">
                                        <h3 class="font-semibold text-sm text-zinc-900 dark:text-white flex items-center gap-1.5">
                                            <span class="inline-block size-2 rounded-full bg-blue-500"></span>
                                            {{ $role->name }}
                                        </h3>
                                    </div>
                                    @if (!empty($role->abilities_description))
                                        <div class="flex flex-wrap gap-1.5 pt-1">
                                            @foreach ($role->abilities_description as $ability)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">
                                                    {{ $ability }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">{{ __('No capabilities details defined.') }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-6 text-center">
                            <flux:icon name="shield-exclamation" class="size-10 text-zinc-300 dark:text-zinc-600 mb-2" />
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('No roles or permissions assigned to this user.') }}</p>
                            <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">{{ __('You can assign roles from the action menu.') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="closeRolesModal" variant="ghost">
                        {{ __('Close') }}
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
