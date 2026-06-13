<!-- Report Container -->
<div
    class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
    {{-- Mobile Cards View (Hidden on MD and up) --}}
    <div class="md:hidden divide-y divide-zinc-200 dark:divide-zinc-700 print:hidden">
        @forelse($groups as $group)
            <div class="p-4 space-y-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ $group['name'] }}</span>
                    <div class="flex gap-2">
                        <flux:badge color="green" size="sm">{{ $group['present_count'] }}
                            {{ __('Present') }}</flux:badge>
                        <flux:badge color="red" size="sm">{{ $group['absent_count'] }} {{ __('Absent') }}
                        </flux:badge>
                    </div>
                </div>

            </div>
        @empty
            <div class="p-8 text-center text-sm text-zinc-500 italic">
                {{ __('No groups found.') }}
            </div>
        @endforelse
    </div>

    {{-- Desktop Table View (Hidden on small screens, shown on print) --}}
    <div class="hidden md:block print:block overflow-x-auto">
        <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Group Name') }}
                    </th>

                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Present') }}
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
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

                        <td
                            class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-green-600 dark:text-green-400">
                            {{ $group['present_count'] }}
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-red-600 dark:text-red-400">
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
</div>
