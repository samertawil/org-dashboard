<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Camp Residents') }}</flux:heading>
            <flux:subheading>{{ __('Manage camp residents and their details.') }}</flux:subheading>
        </div>

        <div class="flex gap-2">
            @can('displacement.camps.create')
            <flux:modal.trigger name="import-modal">
                <flux:button variant="ghost" icon="document-arrow-up">{{ __('Import Excel') }}</flux:button>
            </flux:modal.trigger>
            <flux:button href="{{ route('camps.residents.create') }}" wire:navigate variant="primary" icon="plus">
                {{ __('Add Resident') }}
            </flux:button>
            @endcan
        </div>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Error Message --}}
    @if (session('error'))
        <div class="rounded-md bg-red-50 dark:bg-red-900/50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <flux:icon name="x-circle" class="h-5 w-5 text-red-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                        {{ __('Import Errors') }}
                    </h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                        <p>{!! session('error') !!}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Search and Table Section --}}
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 relative">
            <flux:input wire:model.live.debounce.300ms="search" :placeholder="__('Search by name, identity, status, or camp name...')"
                icon="magnifying-glass" />
            <div wire:loading wire:target="search" class="absolute right-6 top-1/2 -translate-y-1/2">
                <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
            </div>
        </div>

        @if ($search)
            <div class="mt-4 flex items-center justify-end">
                <flux:button wire:click="$set('search', '');" variant="ghost" size="sm" icon="x-mark">
                    {{ __('Clear Filters') }}
                </flux:button>
            </div>
        @endif

        <div class="overflow-x-auto">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                        {{ __('Showing') }}
                        <span
                            class="font-medium text-zinc-900 dark:text-white">{{ $this->residents->firstItem() }}</span>
                        {{ __('to') }}
                        <span
                            class="font-medium text-zinc-900 dark:text-white">{{ $this->residents->lastItem() }}</span>
                        {{ __('of') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $this->residents->total() }}</span>
                        {{ __('results') }}
                    </p>
                </div>
            </div>
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th wire:click="sortBy('full_name')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Name') }}
                                @if ($sortField === 'full_name')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Identity Number') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Camp') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Gender') }}
                        </th>
                         <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Resident Type') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Activation') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->residents as $resident)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $resident->full_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $resident->identity_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $resident->displacementCamp?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                              @php $genderOp = $resident->gender ? \App\Enums\GlobalSystemConstant::options()->where('type','gender')->where('value', $resident->gender)->first() : null; @endphp
                              @if($genderOp)
                                  <div class="flex items-center gap-1">
                                      <span>{{ $genderOp['label'] }}</span>
                                      <span>{!! $genderOp['icon'] ?? '' !!}</span>
                                  </div>
                              @else
                                  -
                              @endif
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $resident->status?->status_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @php
                                    $statusEnum = \App\Enums\GlobalSystemConstant::tryFrom($resident->activation);
                                @endphp
                                @if ($statusEnum)
                                    <span @class([
                                        'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                        'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' =>
                                            $resident->activation == 1,
                                        'bg-zinc-100 text-zinc-700 dark:bg-zinc-500/20 dark:text-zinc-400' =>
                                            $resident->activation != 1,
                                    ])>
                                        {{ $statusEnum->label() }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button href="{{ route('camps.residents.edit', $resident) }}" wire:navigate
                                        variant="ghost" size="sm" icon="pencil-square" />
                                    <flux:button wire:click="delete({{ $resident->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this resident?') }}"
                                        variant="ghost" size="sm" icon="trash"
                                        class="text-red-500 hover:text-red-600" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-zinc-500">
                                {{ __('No residents found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $this->residents->links() }}
        </div>
    </div>

    {{-- Import Modal --}}
    <flux:modal name="import-modal" class="md:w-96">
        <div class="p-6 space-y-6">
            <div class="flex flex-col gap-1">
                <flux:heading size="lg">{{ __('Import Camp Residents') }}</flux:heading>
                <flux:subheading>{{ __('Upload an Excel file to import residents.') }}</flux:subheading>
            </div>

            <form wire:submit="import">
                <flux:field>
                    <flux:label>{{ __('Excel File') }}</flux:label>
                    <input type="file" wire:model="excelFile"
                        class="block w-full text-sm text-zinc-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100
                    " />
                    <flux:error name="excelFile" />
                </flux:field>

                <div class="flex justify-end gap-2 mt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">{{ __('Import') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
