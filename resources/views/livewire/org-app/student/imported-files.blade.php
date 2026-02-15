<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Imported Files') }}</flux:heading>
            <flux:subheading>{{ __('Archive of imported student Excel files.') }}</flux:subheading>
        </div>
      
        <div class="flex gap-2">
            @can('student.index')
            <flux:button href="{{ route('student.index') }}" wire:navigate variant="ghost" icon="arrow-left">
                {{ __('Back to Students') }}
            </flux:button>
            @endcan
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
             <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('File Name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Date Imported') }}
                        </th>
                         <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Size') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($files as $file)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $file['name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ \Carbon\Carbon::createFromTimestamp($file['last_modified'])->format('Y-m-d H:i:s') }}
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ round($file['size'] / 1024, 2) }} KB
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <flux:button wire:click="download('{{ $file['path'] }}')" variant="ghost" size="sm" icon="arrow-down-tray">
                                    {{ __('Download') }}
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-zinc-500">
                                {{ __('No imported files found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
