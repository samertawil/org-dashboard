<div class="flex flex-col gap-6">
    <!-- Header & Actions -->
    <div class="flex items-center justify-between print:hidden">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Groups Attendance Summary') }}</flux:heading>
            <flux:subheading>{{ __('Attendance statistics for all student groups.') }}</flux:subheading>
        </div>
        <div class="flex gap-2">
            <flux:button onclick="window.print()" icon="printer" variant="primary">
                {{ __('Print Report') }}
            </flux:button>
        </div>
    </div>

    <!-- Print Header -->
    <div class="hidden print:block text-center mb-8">
         <h1 class="text-2xl font-bold">{{ __('Groups Attendance Summary') }}</h1>
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
        <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Group Name') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Region') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('City') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Neighborhood') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Present') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Absent') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($groups as $group)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                            {{ $group['name'] }}
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                            {{ $group['region'] }}
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                            {{ $group['city'] }}
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                            {{ $group['neighbourhood'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-green-600 dark:text-green-400">
                            {{ $group['present_count'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-red-600 dark:text-red-400">
                            {{ $group['absent_count'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-zinc-500">
                            {{ __('No groups found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <style>
        @media print {
            body { visibility: hidden; }
            body {
                visibility: visible;
                background: white;
                color: black;
            }
            nav, header, aside, .sidebar { display: none !important; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 8px; }
            .print\:hidden { display: none !important; }
            .print\:block { display: block !important; }
        }
    </style>
</div>
