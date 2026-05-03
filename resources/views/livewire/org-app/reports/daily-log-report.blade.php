<div class="flex flex-col gap-6" x-data="{ mentionableUsers: @js($this->mentionableUsers) }">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Daily Activity Entry Log') }}</flux:heading>
            <flux:subheading>{{ __('Listing all activities entered into the system on a specific day.') }}</flux:subheading>
        </div>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <flux:input type="date" wire:model.live.debounce.1500ms="reportDate" class="flex-1 sm:flex-none" />
            <flux:button icon="printer" onclick="window.print()" class="flex-1 sm:flex-none">{{ __('Print Log') }}</flux:button>
            <flux:button 
                variant="primary" 
                icon="chat-bubble-left-right" 
                wire:click="sendToWhatsApp"
                wire:loading.attr="disabled"
                class="bg-green-600 hover:bg-green-700 text-white w-full sm:w-auto"
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

                {{-- Comments Section --}}
                <div class="mt-8 border-t border-zinc-100 dark:border-zinc-800 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <button wire:click="toggleComments({{ $activity->id }})" class="flex items-center gap-2 text-zinc-500 hover:text-indigo-600 transition-colors group">
                            <flux:icon icon="chat-bubble-left-right" class="size-5 group-hover:scale-110 transition-transform" />
                            <span class="text-sm font-bold">{{ __('Comments') }} ({{ $activity->comments->count() }})</span>
                        </button>
                    </div>

                    @if($showComments[$activity->id] ?? false)
                        {{-- Comment List --}}
                        <div class="space-y-3 mb-6 max-h-80 overflow-y-auto pr-1">
                            @foreach($activity->comments as $comment)
                                <div wire:key="comment-{{ $comment->id }}" class="flex gap-3 group/comment">
                                    <div class="flex-shrink-0">
                                        @if($comment->creator?->google_id && $comment->creator?->avatar)
                                            <img src="{{ $comment->creator->avatar }}" class="w-8 h-8 rounded-full object-cover" alt="{{ $comment->creator->name }}">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-[10px] font-bold text-indigo-600 dark:text-indigo-300">
                                                {{ $comment->creator?->initials() ?? '?' }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="bg-zinc-50 dark:bg-zinc-800/40 rounded-xl rounded-tl-none px-4 py-2 relative border border-zinc-100 dark:border-zinc-800/50">
                                            <div class="flex items-center justify-between gap-2 mb-1">
                                                <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">
                                                    {{ $comment->creator->name ?? '-' }}
                                                </span>
                                                <span class="text-[10px] text-zinc-400">
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed break-words">
                                                {!! preg_replace('/@([\w\x{0600}-\x{06FF}\s]+?)(?=\s|$|@)/u', '<span class="text-indigo-600 dark:text-indigo-400 font-bold">@$1</span>', e($comment->comment)) !!}
                                            </p>
                                            
                                            @if($comment->created_by === auth()->id() || auth()->user()->can('activity.create'))
                                                <button wire:click="deleteComment({{ $comment->id }})" 
                                                    wire:confirm="{{ __('Are you sure?') }}"
                                                    class="absolute -top-2 -right-2 hidden group-hover/comment:flex items-center justify-center w-5 h-5 rounded-full bg-rose-500 text-white shadow text-[10px] hover:bg-rose-600 transition">
                                                    ✕
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- New Comment Input --}}
                        <div x-data="activityComments({{ $activity->id }}, mentionableUsers)" class="relative">
                            <div class="flex gap-2 items-end">
                                <div class="flex-shrink-0 mb-1">
                                    @if(auth()->user()->google_id && auth()->user()->avatar)
                                        <img src="{{ auth()->user()->avatar }}" class="w-8 h-8 rounded-full object-cover" alt="">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/40 flex items-center justify-center text-[10px] font-bold text-indigo-600 dark:text-indigo-300">
                                            {{ auth()->user()->initials() }}
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 relative">
                                    <textarea
                                        x-ref="commentInput"
                                        x-on:input="onInput($event)"
                                        wire:model="newComments.{{ $activity->id }}"
                                        rows="1"
                                        placeholder="{{ __('Write a comment… type @ to mention') }}"
                                        class="w-full text-sm rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100 placeholder-zinc-400 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 resize-none transition"
                                        x-on:keydown.enter.prevent="if(!$event.shiftKey && !showDropdown) { $wire.addComment({{ $activity->id }}); showDropdown = false; }"
                                    ></textarea>
                                    
                                    {{-- @Mention Modal (Centered) --}}
                                    <div x-show="showDropdown"
                                         class="fixed inset-0 z-[100] flex items-center justify-center p-4"
                                         style="display:none"
                                         @click.self="showDropdown = false"
                                         @keydown.escape.window="showDropdown = false">
                                        
                                        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

                                        <div x-show="showDropdown"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                             class="relative w-full max-w-sm bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-700 flex flex-col overflow-hidden">
                                            
                                            <div class="flex items-center justify-between px-5 py-3 border-b border-zinc-100 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">
                                                <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ __('Select a team member') }}</span>
                                                <button @click="showDropdown = false" type="button" class="text-zinc-400 hover:text-zinc-600 transition">✕</button>
                                            </div>

                                            <div class="max-h-[40vh] overflow-y-auto py-2">
                                                <template x-for="user in filteredUsers" :key="user.id">
                                                    <button type="button"
                                                        x-on:mousedown.prevent="selectUser(user)"
                                                        class="w-full flex items-center gap-3 px-5 py-3 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition text-left border-b border-zinc-50 dark:border-zinc-800/50 last:border-0">
                                                        <span class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 text-sm font-bold flex items-center justify-center flex-shrink-0"
                                                            x-text="user.name.charAt(0).toUpperCase()"></span>
                                                        <span x-text="user.name" class="font-medium truncate"></span>
                                                    </button>
                                                </template>
                                                
                                                <div x-show="filteredUsers.length === 0" class="px-5 py-8 text-center flex flex-col items-center gap-2">
                                                    <p class="text-sm text-zinc-500">{{ __('No users found matching:') }}</p>
                                                    <span class="text-xs font-semibold px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-md" x-text="mentionQuery"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button wire:click="addComment({{ $activity->id }})"
                                    class="flex-shrink-0 mb-1 p-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white shadow transition"
                                    wire:loading.attr="disabled">
                                    <flux:icon icon="paper-airplane" size="xs" variant="mini" />
                                </button>
                            </div>
                            @error('newComments.' . $activity->id)
                                <p class="text-rose-500 text-[10px] font-bold mt-1 px-10">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
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

    @script
    <script>
    Alpine.data('activityComments', (activityId, users) => ({
        activityId: activityId,
        allUsers: users,
        showDropdown: false,
        mentionStart: -1,
        mentionQuery: '',
        filteredUsers: [],

        onInput(event) {
            const textarea   = event.target;
            const val        = textarea.value;
            const pos        = textarea.selectionStart;
            const textBefore = val.slice(0, pos);
            const lastAt     = textBefore.lastIndexOf('@');

            if (lastAt === -1) {
                this.showDropdown = false;
                return;
            }

            const query = textBefore.slice(lastAt + 1);

            if (query.includes(' ')) {
                this.showDropdown = false;
                return;
            }

            this.mentionStart = lastAt;
            this.mentionQuery = query;

            this.filteredUsers = query.length === 0
                ? this.allUsers.slice(0, 8)
                : this.allUsers
                    .filter(u => u.name.toLowerCase().includes(query.toLowerCase()))
                    .slice(0, 8);

            this.showDropdown = true;
        },

        selectUser(user) {
            const input    = this.$refs.commentInput;
            const before   = input.value.slice(0, this.mentionStart);
            const after    = input.value.slice(input.selectionStart);
            const inserted = '@' + user.name + ' ';
            
            const newValue = before + inserted + after;
            
            // Update Livewire model
            this.$wire.set('newComments.' + this.activityId, newValue);
            
            this.showDropdown = false;
            
            // Return focus
            this.$nextTick(() => {
                input.focus();
                const newPos = before.length + inserted.length;
                input.setSelectionRange(newPos, newPos);
            });
        }
    }));
    </script>
    @endscript
</div>
