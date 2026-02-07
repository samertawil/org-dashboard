<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Employees') }}</flux:heading>
            <flux:subheading>{{ __('Manage your organization employees.') }}</flux:subheading>
        </div>
        
        <flux:button 
            href="{{ route('employee.create') }}" 
            wire:navigate 
            variant="primary"
            icon="plus"
        >
            {{ __('Add Employee') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Search and Table Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 relative">
            <flux:input 
                wire:model.live="search" 
                :placeholder="__('Search by name, employee number or department...')" 
                icon="magnifying-glass"
            />
            <div wire:loading wire:target="search" class="absolute right-6 top-1/2 -translate-y-1/2">
                <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
            </div>
        </div>

        @if ($search)
            <div class="p-4 flex items-center justify-end border-b border-zinc-200 dark:border-zinc-700">
                <flux:button wire:click="$set('search', '');" variant="ghost" size="sm" icon="x-mark">
                    {{ __('Clear Filters') }}
                </flux:button>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('EMP#') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Department') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Position') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Gender') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Status') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-zinc-600 dark:text-zinc-400">
                                {{ $employee->employee_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $employee->full_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $employee->department->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $employee->positionStatus->status_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                @php
                                    $genderEnum = \App\Enums\GlobalSystemConstant::tryFrom($employee->gender);
                                @endphp
                                @if($genderEnum)
                                    <span class="flex items-center gap-1">
                                        <span>{{ $genderEnum->icon() }}</span>
                                        <span>{{ $genderEnum->label() }}</span>
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @php
                                    $statusEnum = \App\Enums\GlobalSystemConstant::tryFrom($employee->activation);
                                @endphp
                                @if($statusEnum)
                                    <span @class([
                                        'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                        'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' => $employee->activation == 1,
                                        'bg-zinc-100 text-zinc-700 dark:bg-zinc-500/20 dark:text-zinc-400' => $employee->activation != 1,
                                    ])>
                                        {{ $statusEnum->label() }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button 
                                        href="{{ route('employee.edit', $employee) }}" 
                                        wire:navigate 
                                        variant="ghost" 
                                        size="sm" 
                                        icon="pencil-square" 
                                    />
                                    <flux:button 
                                        wire:click="delete({{ $employee->id }})" 
                                        wire:confirm="{{ __('Are you sure you want to delete this employee?') }}"
                                        variant="ghost" 
                                        size="sm" 
                                        icon="trash" 
                                        class="text-red-500 hover:text-red-600"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-zinc-500">
                                {{ __('No employees found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $employees->links() }}
        </div>
    </div>
</div>
