<div class="flex flex-col gap-6">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 print:hidden">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Attendance Report') }}: {{ $group->name }}</flux:heading>
            <flux:subheading>{{ __('Attendance history and statistics.') }}</flux:subheading>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <span title="{{ __('Print the current report') }}" class="flex-1 sm:flex-none">
                <flux:button onclick="window.print()" icon="printer" variant="primary" class="w-full">
                    {{ __('Print Report') }}
                </flux:button>
            </span>
            <span title="{{ __('Return to education points list') }}" class="flex-1 sm:flex-none">
                <flux:button href="{{ route('student.group.index') }}" wire:navigate variant="ghost" icon="arrow-left" class="w-full">
                    {{ __('Back to Groups') }}
                </flux:button>
            </span>
        </div>
    </div>

    <!-- Print Header (Visible only when printing) -->
    <div class="hidden print:block text-center mb-8">
         <h1 class="text-2xl font-bold">{{ __('Attendance Report') }}</h1>
         <h2 class="text-xl">{{ $group->name }}</h2>
         <p class="text-sm text-gray-500">{{ __('From') }}: {{ $dateFrom }} - {{ __('To') }}: {{ $dateTo }}</p>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm print:hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:input type="date" wire:model.live="dateFrom" label="{{ __('Date From') }}" />
            <flux:input type="date" wire:model.live="dateTo" label="{{ __('Date To') }}" />
        </div>
    </div>

    <!-- Report Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        {{-- Mobile Cards View --}}
        <div class="block md:hidden divide-y divide-zinc-200 dark:divide-zinc-700 print:hidden">
            @forelse($reportData as $row)
                @if($row['is_off_day'])
                    <div class="p-4 bg-red-50 dark:bg-red-900/10 flex justify-between items-center">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ $row['date'] }}</span>
                            <span class="text-xs text-zinc-500">{{ $row['day'] }}</span>
                        </div>
                        <span class="text-sm font-bold text-red-600 dark:text-red-400 uppercase tracking-wider">{{ __('OFF DAY') }}</span>
                    </div>
                @else
                    <div class="p-4 space-y-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <div class="flex justify-between items-center">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ $row['date'] }}</span>
                                <span class="text-xs text-zinc-500">{{ $row['day'] }}</span>
                            </div>
                            <div class="flex gap-3">
                                <div class="flex flex-col items-center">
                                    <span class="text-[10px] text-zinc-400 uppercase">{{ __('Present') }}</span>
                                    <span class="text-sm font-bold text-green-600 dark:text-green-400">{{ $row['present_count'] }}</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <span class="text-[10px] text-zinc-400 uppercase">{{ __('Absent') }}</span>
                                    <span class="text-sm font-bold text-red-600 dark:text-red-400">{{ $row['absent_count'] }}</span>
                                </div>
                            </div>
                        </div>
                        @if($row['subjects'])
                            <div class="text-xs text-zinc-600 dark:text-zinc-300">
                                <span class="text-zinc-400 font-medium">{{ __('Subjects') }}:</span> {{ $row['subjects'] }}
                            </div>
                        @endif
                        @if($row['notes'])
                            <div class="text-xs text-zinc-600 dark:text-zinc-300">
                                <span class="text-zinc-400 font-medium">{{ __('Notes') }}:</span> {{ $row['notes'] }}
                            </div>
                        @endif
                    </div>
                @endif
            @empty
                <div class="p-8 text-center text-sm text-zinc-500 italic">
                    {{ __('No records found for the selected period.') }}
                </div>
            @endforelse
        </div>

        {{-- Desktop Table View (and Print View) --}}
        <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700 hidden md:table print:table">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Date') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Subjects') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Present') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Absent') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Notes') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($reportData as $row)
                    @if($row['is_off_day'])
                         <tr class="bg-red-50 dark:bg-red-900/10">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $row['date'] }} <span class="text-xs text-zinc-500">({{ $row['day'] }})</span>
                            </td>
                            <td colspan="4" class="px-6 py-4 text-center text-sm font-bold text-red-600 dark:text-red-400">
                                {{ __('OFF DAY') }}
                            </td>
                         </tr>
                    @else
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $row['date'] }} <span class="text-xs text-zinc-500">({{ $row['day'] }})</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $row['subjects'] ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-green-600 dark:text-green-400">
                                {{ $row['present_count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-red-600 dark:text-red-400">
                                {{ $row['absent_count'] }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $row['notes'] }}
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-zinc-500 italic">
                            {{ __('No records found for the selected period.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <style>
        @media print {
            body { visibility: hidden; }
            .print\:block { display: block !important; }
            .print\:hidden { display: none !important; }
            
            /* Make sure the main content is visible and takes up full width/height */
            /* We need to target the Livewire component root div usually or just body > * */
            /* But better approach: */
            /* Visibility hidden on body hides everything. */
            /* Then visibility visible on the component we want to print. */
            
            /* Actually, simpler approach for "Print This Section" style: */
            /* But simplest is standard print styles */
            
            /* Reset body for printing */
            body {
                visibility: visible;
                background: white;
                color: black;
            }
            
            /* Hide sidebar, nav, etc if they have classes or generic hiding */
            nav, header, aside, .sidebar { display: none !important; }

            /* Ensure table looks good */
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 8px; }
        }
    </style>
</div>
