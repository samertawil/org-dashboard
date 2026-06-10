<div class="flex flex-col gap-6">

    {{-- ══ Page Header ══════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <flux:button
                href="{{ $source === 'supervisor_activities' ? route('reports.supervisor.activities.report') : route('dashboard') }}"
                wire:navigate variant="ghost" size="sm" icon="arrow-right" class="text-zinc-500" />
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <flux:heading level="1" size="xl">{{ __('Create Report') }}</flux:heading>
                    @if ($source === 'supervisor_activities')
                        <flux:badge color="indigo" size="sm">
                            <flux:icon name="academic-cap" class="size-3 mr-1" />
                            {{ __('From Activities') }}
                        </flux:badge>
                    @endif
                </div>
                <flux:subheading>{{ __('Fill in the report details, recipients and summary items.') }}</flux:subheading>
            </div>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <flux:button
                href="{{ $source === 'supervisor_activities' ? route('reports.supervisor.activities.report') : route('dashboard') }}"
                wire:navigate variant="ghost" class="flex-1 sm:flex-none">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button wire:click="saveReport" variant="primary" icon="paper-airplane" wire:loading.attr="disabled"
                class="flex-1 sm:flex-none">
                <span wire:loading.remove wire:target="saveReport">{{ __('Save & Send') }}</span>
                <span wire:loading wire:target="saveReport">{{ __('Saving...') }}</span>
            </flux:button>
        </div>
    </div>

    {{-- ══ Session Feedback ══════════════════════════════════════════════ --}}
    <x-auth-session-status class="text-center" :status="session('message')" />
    @if (session('error'))
        <div
            class="p-3 bg-red-100 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 text-center rounded-lg text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="saveReport" class="flex flex-col gap-6">

        {{-- ══ Section 1: Report Information ══════════════════════════════ --}}
        <flux:card>
            <div class="flex items-center gap-2 mb-5 pb-3 border-b border-zinc-200 dark:border-zinc-700">
                <flux:icon name="document-text" class="size-5 text-zinc-500" />
                <flux:heading size="lg">{{ __('Report Information') }}</flux:heading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Report Name --}}
                <div class="md:col-span-2">
                    <flux:input wire:model="report_name" label="{{ __('Report Name') }}"
                        placeholder="{{ __('e.g. Monthly Educational Activities Report') }}" required />
                    @error('report_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Report Date --}}
                <flux:input type="date" wire:model="report_date" label="{{ __('Report Date') }}" required />

                {{-- Batch No (read-only if pre-filled) --}}
                @if ($batchNo)
                    <flux:input type="text" value="{{ $batchNo }}" label="{{ __('Batch Number') }}"
                        disabled />
                @endif

                {{-- Date From --}}
                <flux:input type="date" wire:model="date_from" label="{{ __('Period From') }}" required />

                {{-- Date To --}}
                <flux:input type="date" wire:model="date_to" label="{{ __('Period To') }}" required />

                {{-- Period Type --}}
                <div class="flex flex-col gap-1">
                    <flux:label>{{ __('Report Period Type') }} <span class="text-red-500">*</span></flux:label>
                    <flux:select wire:model="report_period_type" required>
                        <option value="">-- {{ __('Select Period Type') }} --</option>
                        @foreach ($periodTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->status_name }}</option>
                        @endforeach
                    </flux:select>
                    @error('report_period_type')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Main Type --}}
                <div class="flex flex-col gap-1">
                    <flux:label>{{ __('Report Main Type') }} <span class="text-red-500">*</span></flux:label>
                    <flux:select wire:model="report_main_type" required>
                        <option value="">-- {{ __('Select Main Type') }} --</option>
                        @foreach ($mainTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->status_name }}</option>
                        @endforeach
                    </flux:select>
                    @error('report_main_type')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </flux:card>

        {{-- ══ Section 2: Recipients ═══════════════════════════════════════ --}}
        <flux:card>
            <div class="flex items-center gap-2 mb-5 pb-3 border-b border-zinc-200 dark:border-zinc-700">
                <flux:icon name="users" class="size-5 text-zinc-500" />
                <flux:heading size="lg">{{ __('Recipients') }}</flux:heading>
            </div>

            <div class="flex flex-col gap-5">
                {{-- Addressed To Employee --}}
                <div class="flex flex-col gap-1">
                    <flux:label>{{ __('Addressed To Employee') }} <span class="text-red-500">*</span></flux:label>
                    <flux:select wire:model="addressed_to_employees" required>
                        <option value="">-- {{ __('Select Employee') }} --</option>
                        @foreach ($allEmployees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                        @endforeach
                    </flux:select>
                    @error('addressed_to_employees')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Addressed To Department Types --}}
                <div class="flex flex-col gap-2 my-5">
                    <flux:label>{{ __('Addressed To Departments') }} <span class="text-red-500">*</span></flux:label>
                    <div
                        class="flex flex-wrap gap-4 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">
                        <flux:checkbox wire:model="addressed_to_dept_types" value="center_director"
                            label="{{ __('Center Director') }}" />
                        <flux:checkbox wire:model="addressed_to_dept_types" value="educational_coordinator"
                            label="{{ __('Educational Coordinator') }}" />
                        <flux:checkbox wire:model="addressed_to_dept_types" value="management"
                            label="{{ __('Management') }}" />
                    </div>
                    @error('addressed_to_dept_types')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CC / Follow Up By ──────────────── --}}
                <div class="flex flex-col gap-2">
                    <flux:label>{{ __('Follow Up By (CC)') }}</flux:label>

                    {{-- Selected CC Badges --}}
                    @if (!empty($selectedCcEmployees) && count($selectedCcEmployees) > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach ($selectedCcEmployees as $selectedEmp)
                                <flux:badge variant="outline" color="indigo" size="sm"
                                    class="flex items-center gap-1.5 px-2.5 py-1">
                                    {{ $selectedEmp->full_name }}
                                    <button type="button" wire:click="removeCcEmployee({{ $selectedEmp->id }})"
                                        class="text-zinc-400 hover:text-red-500 transition-colors ml-1">
                                        <flux:icon name="x-mark" class="size-3" />
                                    </button>
                                </flux:badge>
                            @endforeach
                        </div>
                    @endif

                    {{-- Autocomplete Input --}}
                    <div class="relative">
                        <flux:input wire:model.live.debounce.300ms="ccSearch"
                            placeholder="{{ __('Search employee name…') }}" icon="magnifying-glass" />

                        @if (!empty($filteredCcEmployees) && count($filteredCcEmployees) > 0)
                            <div
                                class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg max-h-56 overflow-y-auto">
                                @foreach ($filteredCcEmployees as $emp)
                                    <button type="button" wire:click="addCcEmployee({{ $emp->id }})"
                                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-700/50 border-b last:border-b-0 border-zinc-100 dark:border-zinc-700/50 transition-colors flex justify-between items-center gap-3">
                                        <div>
                                            <span
                                                class="font-medium text-zinc-800 dark:text-zinc-200">{{ $emp->full_name }}</span>
                                            @if ($emp->email)
                                                <span class="text-xs text-zinc-400 block">{{ $emp->email }}</span>
                                            @endif
                                        </div>
                                        <flux:icon name="plus-circle" class="size-4 text-indigo-400 flex-shrink-0" />
                                    </button>
                                @endforeach
                            </div>
                        @elseif(trim($ccSearch) !== '')
                            <div
                                class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg p-4 text-center text-xs text-zinc-400">
                                {{ __('No employees found.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </flux:card>

        {{-- ══ Section 3: Report Body Items ═════════════════════════════════ --}}
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:heading size="lg">{{ __('Report Body') }}</flux:heading>
                    <flux:badge color="amber" size="sm">{{ count($reportItems) }} {{ __('items') }}
                    </flux:badge>
                </div>
                <flux:button wire:click="addItem" type="button" variant="outline" size="sm" icon="plus">
                    {{ __('Add Item') }}
                </flux:button>
            </div>

            @foreach ($reportItems as $index => $item)
                <flux:card wire:key="report-item-{{ $index }}" class="border-l-4 border-l-indigo-400">
                    {{-- Item Header --}}
                    <div
                        class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center gap-2">
                            <flux:badge color="indigo" size="sm">{{ $index + 1 }}</flux:badge>
                            @if (!empty($item['title']))
                                <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 truncate max-w-xs">
                                    {{ $item['title'] }}
                                </span>
                            @else
                                <span class="text-sm text-zinc-400 italic">{{ __('Untitled Item') }}</span>
                            @endif
                        </div>
                        @if (count($reportItems) > 1)
                            <flux:button wire:click="removeItem({{ $index }})" type="button" variant="ghost"
                                size="sm" icon="trash" class="text-red-500 hover:text-red-700" />
                        @endif
                    </div>

                    <div class="flex flex-col gap-4">
                        {{-- Item Title --}}
                        <flux:input wire:model="reportItems.{{ $index }}.title"
                            label="{{ __('Item Title') }}"
                            placeholder="{{ __('e.g. First Aid Activity Summary') }}" />

                        {{-- Content --}}
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center justify-between">
                                <flux:label>{{ __('Summary Content') }} <span class="text-red-500">*</span></flux:label>
                                @if (config('services.gemini.key'))
                                    <button type="button" wire:click="summarizeItemWithAI({{ $index }})" 
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 disabled:opacity-50 transition-colors">
                                        <flux:icon name="sparkles" class="size-3.5" wire:loading.remove wire:target="summarizeItemWithAI({{ $index }})" />
                                        <flux:icon name="arrow-path" class="size-3.5 animate-spin text-zinc-500" wire:loading wire:target="summarizeItemWithAI({{ $index }})" />
                                        <span>{{ __('AI Summarize') === 'AI Summarize' ? 'تلخيص ذكي (AI)' : __('AI Summarize') }}</span>
                                    </button>
                                @endif
                            </div>
                            <textarea wire:model="reportItems.{{ $index }}.content" rows="6"
                                placeholder="{{ __('Write the consolidated summary for this activity…') }}"
                                class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition resize-y leading-relaxed"></textarea>
                            @error("reportItems.{$index}.content")
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Observation --}}
                        <flux:input wire:model="reportItems.{{ $index }}.observation"
                            label="{{ __('Observation') }}"
                            placeholder="{{ __('Any observation or follow-up notes…') }}" />

                        {{-- Attachments Pool --}}
                        @if (!empty($item['attachments_pool']))
                            <div class="flex flex-col gap-2">
                                <flux:label class="text-xs uppercase tracking-wide text-zinc-400">
                                    {{ __('Select Attachments to Include') }}
                                </flux:label>
                                <div
                                    class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-8 gap-2 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700">
                                    @foreach ($item['attachments_pool'] as $attIndex => $att)
                                        @php
                                            $url = is_array($att) ? $att['url'] ?? ($att['path'] ?? '') : $att;
                                            $name = is_array($att) ? $att['name'] ?? __('File') : __('File');
                                        @endphp
                                        @if ($url)
                                            <label
                                                class="relative border border-zinc-200 dark:border-zinc-700 rounded-lg p-1.5 cursor-pointer flex flex-col items-center gap-1 bg-white dark:bg-zinc-800 hover:border-indigo-400 transition-colors has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 dark:has-[:checked]:bg-indigo-950/20">
                                                <input type="checkbox"
                                                    wire:model="reportItems.{{ $index }}.selected_attachments"
                                                    value="{{ json_encode($att) }}"
                                                    class="absolute top-1.5 right-1.5 rounded text-indigo-600 focus:ring-indigo-500 border-zinc-300" />
                                                @if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $url))
                                                    <img src="{{ asset($url) }}"
                                                        class="size-12 object-cover rounded" />
                                                @else
                                                    <flux:icon name="document" class="size-12 text-zinc-300" />
                                                @endif
                                                <span
                                                    class="text-[10px] text-zinc-400 truncate w-full text-center leading-tight">{{ $name }}</span>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </flux:card>
            @endforeach

            {{-- Add Item (bottom) --}}
            <button type="button" wire:click="addItem"
                class="w-full py-3 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600 hover:border-indigo-400 dark:hover:border-indigo-600 text-sm text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium transition-colors flex items-center justify-center gap-2">
                <flux:icon name="plus-circle" class="size-4" />
                {{ __('Add Another Item') }}
            </button>
        </div>

        {{-- ══ Section 4: General Notes ══════════════════════════════════════ --}}
        <flux:card>
            <div class="flex items-center gap-2 mb-4">
                <flux:icon name="chat-bubble-left-ellipsis" class="size-5 text-zinc-500" />
                <flux:heading size="lg">{{ __('General Notes') }}</flux:heading>
            </div>
            <textarea wire:model="note" rows="3" placeholder="{{ __('Any general notes or remarks about this report…') }}"
                class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition resize-none leading-relaxed"></textarea>
        </flux:card>

        {{-- ══ Footer Actions ════════════════════════════════════════════════ --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pb-6">
            <p class="text-xs text-zinc-400 hidden sm:block">
                {{ __('Report will be emailed to the addressed employee upon saving.') }}
            </p>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <flux:button
                    href="{{ $source === 'supervisor_activities' ? route('reports.supervisor.activities.report') : route('dashboard') }}"
                    wire:navigate variant="ghost" class="flex-1 sm:flex-none">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary" icon="paper-airplane" wire:loading.attr="disabled"
                    class="flex-1 sm:flex-none">
                    <span wire:loading.remove wire:target="saveReport">{{ __('Save & Send Report') }}</span>
                    <span wire:loading wire:target="saveReport">{{ __('Saving…') }}</span>
                </flux:button>
            </div>
        </div>

    </form>
</div>
