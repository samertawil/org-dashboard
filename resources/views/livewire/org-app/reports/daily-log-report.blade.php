<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Daily Activity Entry Log') }}</flux:heading>
            <flux:subheading>{{ __('Listing all activities entered into the system on a specific day.') }}</flux:subheading>
        </div>
        <div class="flex gap-2">
            <flux:input type="date" wire:model.live="reportDate" />
            <flux:button icon="printer" onclick="window.print()">{{ __('Print Log') }}</flux:button>
            <flux:button 
                variant="primary" 
                icon="chat-bubble-left-right" 
                wire:click="sendToWhatsApp"
                wire:loading.attr="disabled"
                class="bg-green-600 hover:bg-green-700 text-white"
            >
                {{ __('Send to WhatsApp') }}
            </flux:button>
        </div>
    </div>

    @if($exchangeRate)
        <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-xl border border-indigo-100 dark:border-indigo-800/30 flex items-center gap-3">
            <flux:icon icon="currency-dollar" class="text-indigo-600 dark:text-indigo-400" />
            <div class="text-sm">
                <span class="font-bold text-indigo-700 dark:text-indigo-300">{{ __('Current Exchange Rate') }}:</span>
                <span class="text-indigo-600 dark:text-indigo-400">1 USD = {{ $exchangeRate->currency_value }} NIS</span>
                <span class="text-xs text-indigo-400 ml-2">({{ __('As of') }}: {{ $exchangeRate->exchange_date }})</span>
            </div>
        </div>
    @endif

@canany(['activity.index','activity.show','manager.reports.all'])
    <div class="space-y-6">
        @forelse($activities as $activity)
            <flux:card class="overflow-hidden border-l-4 border-l-indigo-500">
                <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <flux:badge size="sm" color="zinc">{{ $activity->created_at->format('H:i') }}</flux:badge>
                            <flux:heading size="lg">{{ $activity->name }}</flux:heading>
                            <span class="text-xs text-zinc-400">({{ $activity->start_date }})</span>
                        </div>
                        <p class="text-sm text-zinc-500 flex items-center gap-2">
                            <flux:icon icon="map-pin" class="size-3" />
                            {{ $activity->regions->region_name ?? '-' }} / {{ $activity->cities->city_name ?? '-' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-indigo-600">{{ number_format($activity->cost, 2) }} $</div>
                        <div class="text-sm text-zinc-500">{{ number_format($activity->cost_nis, 2) }} NIS</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Personnel & Entry --}}
                    <div class="space-y-4">
                        <div>
                            <flux:label class="text-xs uppercase tracking-wider text-zinc-400">{{ __('Entered By') }}</flux:label>
                            <div class="flex items-center gap-2 mt-1">
                                <flux:avatar size="xs" :name="$activity->creator->name ?? 'U'" />
                                <span class="text-sm font-medium">{{ $activity->creator->name ?? 'Unknown' }}</span>
                            </div>
                        </div>

                        <div>
                            <flux:label class="text-xs uppercase tracking-wider text-zinc-400">{{ __('Work Team') }}</flux:label>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @forelse($activity->workTeams as $team)
                                    <flux:badge variant="outline" size="sm" class="flex items-center gap-1">
                                        <flux:icon icon="user" class="size-3" />
                                        {{ $team->employeeRel->full_name ?? 'Employee' }}
                                    </flux:badge>
                                @empty
                                    <span class="text-xs text-zinc-400 italic">{{ __('No team assigned') }}</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Distribution & Beneficiaries --}}
                    <div class="space-y-4">
                        <div>
                            <flux:label class="text-xs uppercase tracking-wider text-zinc-400">{{ __('Parcels Distributed') }}</flux:label>
                            <div class="space-y-1 mt-1">
                                @forelse($activity->parcels as $parcel)
                                    <div class="flex justify-between text-sm p-2 bg-emerald-50 dark:bg-emerald-900/10 rounded border border-emerald-100 dark:border-emerald-800/30">
                                        <span>{{ $parcel->parcelType->status_name ?? 'Parcel' }}</span>
                                        <span class="font-bold">{{ number_format($parcel->distributed_parcels_count) }} {{ $parcel->unit->status_name ?? '' }}</span>
                                    </div>
                                @empty
                                    <span class="text-xs text-zinc-400 italic">{{ __('No parcels recorded') }}</span>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <flux:label class="text-xs uppercase tracking-wider text-zinc-400">{{ __('Beneficiaries') }}</flux:label>
                            <div class="space-y-1 mt-1">
                                @forelse($activity->beneficiaries as $ben)
                                    <div class="flex justify-between text-sm p-2 bg-sky-50 dark:bg-sky-900/10 rounded border border-sky-100 dark:border-sky-800/30">
                                        <span>{{ $ben->beneficiaryType->status_name ?? 'Beneficiary' }}</span>
                                        <span class="font-bold">{{ number_format($ben->beneficiaries_count) }}</span>
                                    </div>
                                @empty
                                    <span class="text-xs text-zinc-400 italic">{{ __('No beneficiaries recorded') }}</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Attachments & Status --}}
                    <div class="space-y-4">
                        <div>
                            <flux:label class="text-xs uppercase tracking-wider text-zinc-400">{{ __('Attachments') }}</flux:label>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @forelse($activity->attachments as $attachment)
                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-200 transition-colors">
                                        <flux:icon icon="paper-clip" class="size-4" />
                                    </a>
                                @empty
                                    <span class="text-xs text-zinc-400 italic">{{ __('No attachments') }}</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </flux:card>
        @empty
            <flux:card class="p-12 text-center">
                <flux:icon icon="no-symbol" class="mx-auto size-12 mb-4 text-zinc-300" />
                <flux:heading>{{ __('No activities entered on this day.') }}</flux:heading>
            </flux:card>
        @endforelse
    </div>
    @endcanany
    {{-- Education Section --}}
    @canany(['student.index','student.show', 'manager.reports.all'])
    <div class="mt-12 space-y-8">
        <div class="flex items-center gap-4">
            <div class="h-px flex-1 bg-zinc-200 dark:bg-zinc-700"></div>
            <flux:heading size="xl" class="flex items-center gap-2">
                <flux:icon icon="academic-cap" class="size-6 text-indigo-500" />
                {{ __('Education Daily Log') }}
            </flux:heading>
            <div class="h-px flex-1 bg-zinc-200 dark:bg-zinc-700"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Evaluations Card --}}
            <flux:card class="p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon icon="document-magnifying-glass" class="size-5" />
                    {{ __('Evaluations & Surveys') }}
                </flux:heading>
                <div class="space-y-3">
                    @forelse($evaluations as $eval)
                        <div class="flex items-center justify-between p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-700">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-indigo-600">{{ $eval->surveyfor->status_name ?? __('Evaluation') }}</span>
                                <span class="text-xs text-zinc-500">{{ __('Survey Type') }}</span>
                            </div>
                            <div class="text-right">
                                <div class="text-xl font-black text-indigo-700 dark:text-indigo-300">{{ $eval->students_count }}</div>
                                <div class="text-xs text-indigo-600">{{ __('Students') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-zinc-500 italic">
                            {{ __('No evaluations recorded today.') }}
                        </div>
                    @endforelse
                </div>
            </flux:card>

            {{-- Attendance Card --}}
            <flux:card class="p-6">
                <flux:heading size="lg" class="mb-4 flex items-center gap-2">
                    <flux:icon icon="users" class="size-5" />
                    {{ __('Attendance Tracking') }}
                </flux:heading>
                <div class="space-y-3">
                    @forelse($attendanceStats as $stat)
                        <div class="flex items-center justify-between p-4 rounded-xl bg-sky-50 dark:bg-sky-900/10 border border-sky-100 dark:border-sky-800/30">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-sky-700 dark:text-sky-300">{{ $stat->studentGroup->name ?? __('Group ID: ') . $stat->student_group_id }}</span>
                                <span class="text-xs text-sky-600 dark:text-sky-400">{{ __('Educational Group') }}</span>
                            </div>
                            <div class="text-right">
                                <div class="text-xl font-black text-sky-700 dark:text-sky-300">{{ $stat->total_entries }}</div>
                                <div class="text-xs text-sky-600">{{ __('Entries') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-zinc-500 italic">
                            {{ __('No attendance entries recorded today.') }}
                        </div>
                    @endforelse
                </div>
            </flux:card>
        </div>
    </div>
        @endcanany
</div>
