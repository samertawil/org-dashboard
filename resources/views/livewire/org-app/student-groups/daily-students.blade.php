<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Daily Attendance') }}: {{ $group->name }}</flux:heading>
            <flux:subheading>{{ __('Students scheduled for') }} {{ $formattedDate }} ({{ $dayName }})</flux:subheading>
        </div>
        <flux:button href="{{ route('student.group.schedule', $group) }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Back to Schedule') }}
        </flux:button>
    </div>

    <!-- Students List -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Name') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Identity Number') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Enrollment Type') }}
                    </th>
                     <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Status') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($students as $student)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                            {{ $student->full_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                            {{ $student->identity_number }}
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                             @if($student->enrollment_type === 'full_week')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                    {{ __('Full Week') }}
                                </span>
                            @elseif($student->enrollment_type === 'sat_mon_wed')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ __('Sat/Mon/Wed') }}
                                </span>
                            @elseif($student->enrollment_type === 'sun_tue_thu')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300">
                                    {{ __('Sun/Tue/Thu') }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @php
                                $statusEnum = \App\Enums\GlobalSystemConstant::tryFrom($student->activation);
                            @endphp
                            @if ($statusEnum)
                                <span @class([
                                    'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                    'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' =>
                                        $student->activation == 1,
                                    'bg-zinc-100 text-zinc-700 dark:bg-zinc-500/20 dark:text-zinc-400' =>
                                        $student->activation != 1,
                                ])>
                                    {{ $statusEnum->label() }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-zinc-500">
                            {{ __('No students scheduled for this day.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
