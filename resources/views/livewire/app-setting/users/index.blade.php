<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading ?? 'Users List' }}</flux:heading>
            <flux:subheading>{{ $subheading ?? 'Details for your new Users below.' }}</flux:subheading>
        </div>

        <flux:button href="{{ route('user.create') }}" wire:navigate variant="primary" icon="plus">
            {{ __('Create New Users') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />



    {{-- Search & Filter Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Search & Filter') }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 relative">
            {{-- Search by Name --}}
            <div class="relative">
                <flux:input wire:model.live.debounce.300ms="search" :label="__('Search by Name')" type="text"
                    :placeholder="__('Search user name...')" icon="magnifying-glass" />
                <div wire:loading wire:target="search" class="absolute right-3 top-[2.4rem]">
                    <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                </div>
            </div>
            
            {{-- Search by Email --}}
            <div class="relative">
                <flux:input wire:model.live.debounce.300ms="searchEmail" :label="__('Search by Email')" type="text"
                    :placeholder="__('Search user email...')" icon="magnifying-glass" />
                <div wire:loading wire:target="searchEmail" class="absolute right-3 top-[2.4rem]">
                    <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                </div>
            </div>

            <div class="relative">
                <flux:select wire:model.live="searchActivation" :label="__('Filter by Status')"
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
            <div class="mt-4 flex items-center justify-end">
                <flux:button wire:click="$set('search', ''); $set('searchEmail', ''); $set('searchActivation', '');" variant="ghost" size="sm"
                    icon="x-mark">
                    {{ __('Clear Filters') }}
                </flux:button>
            </div>
        @endif
    </div>

    {{-- Data Table Section --}}
    @if ($this->users->count() > 0)
        <div
            class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm">
            {{-- Table Header Info --}}
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                        {{ __('Showing') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $this->users->firstItem() }}</span>
                        {{ __('to') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $this->users->lastItem() }}</span>
                        {{ __('of') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $this->users->total() }}</span>
                        {{ __('results') }}
                    </p>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                #
                            </th>
                            <th wire:click="sortBy('name')"
                                class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                <div class="flex items-center gap-1">
                                    {{ __('User Name') }}
                                    @if ($sortField === 'name')
                                        <flux:icon
                                            name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                            class="size-3" />
                                    @else
                                        <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                    @endif
                                </div>
                            </th>


                            <th wire:click="sortBy('email')"
                                class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                <div class="flex items-center gap-1">
                                    {{ __('User Email') }}
                                    @if ($sortField === 'email')
                                        <flux:icon
                                            name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                            class="size-3" />
                                    @else
                                        <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                    @endif
                                </div>
                            </th>

                            <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Activation') }}
                        </th>

                        
                        <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Setting') }}
                    </th>


                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->users as $index => $user)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $this->users->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                            {{ $user->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </td>

                                
                                <td class="px-6 py-3">
                                    <div @class([
                                        'text-sm max-w-xs  truncate',
                                        'text-green-600 dark:text-green-400' => $user->activation == 1,
                                        'text-red-600 dark:text-red-400' => $user->activation == 0,
                                    ])>
                                        {{ data_get(\App\Enums\GlobalSystemConstant::options()->where('type', 'status')->firstWhere('value', $user->activation), 'label', __('No Status')) }}
                                    </div>
                                </td>
 

                                <td class="px-6 py-3 whitespace-nowrap text-center text-sm font-medium">
                                    <flux:dropdown>
                                        <flux:button variant="ghost" icon="cog-6-tooth" size="sm" />

                                        <flux:menu>
                                            <flux:menu.item wire:click="resetPass({{ $user->id }})" wire:confirm="{{ __('are you sure') }}" icon="key">
                                                {{ __('request_need_password') }}
                                            </flux:menu.item>

                                            <flux:menu.item href="{{ route('grant.role.user', $user->id) }}" icon="shield-check" wire:navigate>
                                                {{ __('grant_privileges') }}
                                            </flux:menu.item>

                                            <flux:menu.item  wire:click.prevent="switchActivation({{ $user->id }})"  icon="arrow-path" wire:navigate>
                                                {{ __('Switch Activation') }}
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </td>

                                {{--
                                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button size="sm" variant="ghost" icon="pencil"
                                            :href="route('status.edit', $status)" wire:navigate>
                                            {{ __('Edit') }}
                                        </flux:button>
                                        <flux:button size="sm" variant="danger" icon="trash">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </div>
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                {{ $this->users->links() }}
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div
            class="flex flex-col items-center justify-center py-12 px-4 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-700 mb-4">
                <flux:icon.document-text class="w-8 h-8 text-zinc-400 dark:text-zinc-500" />
            </div>
            <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">
                {{ __('No Users Found') }}
            </h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 text-center mb-6 max-w-sm">
                @if ($search || $searchEmail || $searchActivation)
                    {{ __('No users match your search criteria. Try adjusting your filters.') }}
                @else
                    {{ __('Get started by creating your first user using the form above.') }}
                @endif
            </p>
            @if (!$search && !$searchEmail && !$searchActivation)
                <flux:button href="{{ route('user.create') }}" wire:navigate variant="primary" icon="plus">
                    {{ __('Create New User') }}
                </flux:button>
            @endif
            @if ($search || $searchEmail || $searchActivation)
                <flux:button wire:click="$set('search', ''); $set('searchEmail', ''); $set('searchActivation', '');" variant="primary" icon="x-mark">
                    {{ __('Clear Filters') }}
                </flux:button>
            @endif
        </div>
    @endif
</div>
