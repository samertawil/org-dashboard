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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
            </div>

            {{-- Clear Filters --}}
            @if ($search || $searchEmail || $searchActivation)
                <div class="flex items-center justify-end pt-2">
                    <span title="{{ __('Reset all search filters') }}">
                        <flux:button wire:click="$set('search', ''); $set('searchEmail', ''); $set('searchActivation', '');" variant="ghost" size="sm"
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
                                    <h3 class="font-semibold text-zinc-900 dark:text-white">{{ $user->name }}</h3>
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
                                            <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $user->name }}</span>
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
                                        <flux:dropdown>
                                            <flux:button variant="ghost" icon="cog-6-tooth" size="sm" />
                                            <flux:menu>
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
                @if ($search || $searchEmail || $searchActivation)
                    {{ __('No users match your current filters. Try refining your search or clearing the filters.') }}
                @else
                    {{ __('You haven\'t added any users yet. Start building your team by creating a new user.') }}
                @endif
            </p>
            @if ($search || $searchEmail || $searchActivation)
                <flux:button wire:click="$set('search', ''); $set('searchEmail', ''); $set('searchActivation', '');" variant="primary" icon="x-mark">
                    {{ __('Clear All Filters') }}
                </flux:button>
            @else
                <flux:button href="{{ route('user.create') }}" wire:navigate variant="primary" icon="plus" class="px-8">
                    {{ __('Create First User') }}
                </flux:button>
            @endif
        </div>
    @endif
</div>
