
<div 
    x-data="offlineAttendance({
        groupId: '{{ $group->id }}',
        date: '{{ $date }}',
        students: @js($students->pluck('id'))
    })"
    class="flex flex-col gap-6 landscape-force"
>
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Daily Attendance') }}: {{ $group->name }}</flux:heading>
            <flux:subheading>{{ __('Students scheduled for') }} {{ $formattedDate }} ({{ $dayName }})</flux:subheading>
            <div x-show="!online" x-cloak class="text-amber-600 dark:text-amber-400 text-sm font-medium flex items-center gap-2">
                <flux:icon name="wifi" variant="micro" class="size-4" />
                {{ __('You are offline. Changes will be saved locally.') }}
            </div>
             <div x-show="online && unsyncedCount > 0" x-cloak class="text-blue-600 dark:text-blue-400 text-sm font-medium flex items-center gap-2">
                <flux:icon name="arrow-path" variant="micro" class="size-4 animate-spin" />
                <span x-text="'Syncing ' + unsyncedCount + ' record(s)...'"></span>
            </div>
        </div>
        <flux:button href="{{ route('student.group.schedule', $group) }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Back to Schedule') }}
        </flux:button>
    </div>

    <div class="flex justify-between items-center bg-white dark:bg-zinc-800 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <h2 class="text-lg font-medium text-zinc-900 dark:text-white">{{ __('Students List') }}</h2>
        <div class="flex gap-2">
           
            <flux:button @click="save" variant="primary" x-bind:disabled="saving">
                <span x-show="!saving">{{ __('Save Attendance') }}</span>
                <span x-show="saving">{{ __('Saving...') }}</span>
            </flux:button>
        </div>
    </div>

    <!-- Students List -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <flux:button @click="markAllPresent" variant="primary" size="sm" class="mb-3 mt-3 ms-4">
            {{ __('Mark All Present') }}
        </flux:button>

        <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                         {{ __('Attendance') }}
                    </th>
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
                        <td class="px-6 py-4 whitespace-nowrap">
                             <div class="relative flex items-center w-fit">
                                <flux:checkbox 
                                    x-model="attendance['{{ $student->id }}']" 
                                    @change="updateStatus('{{ $student->id }}')"
                                />
                                <div x-show="attendanceStatus['{{ $student->id }}'] === 'absent' && !attendance['{{ $student->id }}']" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <flux:icon name="x-mark" class="size-4 text-red-600 dark:text-red-500 font-bold" />
                                </div>
                             </div>
                        </td>
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
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-zinc-500">
                            {{ __('No students scheduled for this day.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @push('scripts')
    <script src="/js/offline-attendance.js"></script>
    <script>
        function registerOfflineAttendance() {
            if (!window.Alpine) return;
            if (window.Alpine.data('offlineAttendance')) return;

            Alpine.data('offlineAttendance', (config) => ({
                groupId: config.groupId,
                date: config.date,
                students: config.students,
                attendance: @entangle('attendance'),
                attendanceStatus: @entangle('attendanceStatus'),
                online: navigator.onLine,
                saving: false,
                unsyncedCount: 0,

                async init() {
                    window.addEventListener('online', () => {
                        this.online = true;
                        this.syncOfflineData();
                    });
                    window.addEventListener('offline', () => this.online = false);
                    
                    await this.checkUnsynced();
                    if (this.online && this.unsyncedCount > 0) {
                        this.syncOfflineData();
                    }
                },

                updateStatus(studentId) {
                    // Logic mirrored from PHP side roughly
                },

                markAllPresent() {
                   this.students.forEach(id => {
                       this.attendance[id] = true;
                   });
                },

                async save() {
                    this.saving = true;
                    
                    if (this.online) {
                        try {
                            await this.$wire.saveAttendance();
                        } catch (e) {
                            console.error(e);
                        }
                    } else {
                        try {
                            const promises = this.students.map(studentId => {
                                const isPresent = this.attendance[studentId] || false;
                                const status = isPresent ? 'present' : 'absent';
                                this.attendanceStatus[studentId] = status;

                                return window.OfflineAttendance.saveAttendance(
                                    this.groupId, 
                                    this.date, 
                                    studentId, 
                                    status
                                );
                            });

                            await Promise.all(promises);
                            this.checkUnsynced();
                            alert('Attendance saved locally. Will sync when online.');
                        } catch (e) {
                            console.error('Offline save failed', e);
                        }
                    }
                    
                    this.saving = false;
                },

                async checkUnsynced() {
                     const records = await window.OfflineAttendance.getUnsyncedRecords();
                     this.unsyncedCount = records.length;
                },

                async syncOfflineData() {
                    const records = await window.OfflineAttendance.getUnsyncedRecords();
                    if (records.length === 0) return;

                    const pageRecords = records.filter(r => r.groupId === this.groupId && r.date === this.date);
                    
                    if (pageRecords.length > 0) {
                        try {
                            console.log('Starting sync for', pageRecords.length, 'records');
                            pageRecords.forEach(r => {
                                this.attendance[r.studentId] = (r.status === 'present');
                            });
                            
                            await this.$wire.saveAttendance();
                            
                            const deletePromises = pageRecords.map(r => window.OfflineAttendance.markAsSynced(r.id));
                            await Promise.all(deletePromises);
                        } catch (error) {
                            console.error('Sync failed:', error);
                        } finally {
                            await this.checkUnsynced();
                        }
                    } else {
                        await this.checkUnsynced();
                    }
                }
            }));
        }

        if (window.Alpine) {
            registerOfflineAttendance();
        } else {
            document.addEventListener('alpine:init', registerOfflineAttendance);
        }
    </script>
    @endpush
</div>
