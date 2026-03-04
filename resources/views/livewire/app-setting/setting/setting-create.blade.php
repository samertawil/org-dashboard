<div class="flex flex-col gap-6 p-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('System Settings') }}</flux:heading>
            <flux:subheading>{{ __('Define and manage system-wide configurations and AI context parameters.') }}</flux:subheading>
        </div>

        <flux:button href="{{ route('setting.index') }}" wire:navigate variant="primary" icon="list-bullet">
            {{ __('Settings List') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center {{ session('type') == 'error' ? 'text-red-500' : '' }}"
        :status="session('message')" />

    {{-- Form Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <form wire:submit="store" class="p-6 space-y-8">
            
            {{-- Section 1: Identity & Primary Value --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2">
                    <flux:heading size="lg" class="text-indigo-600 dark:text-indigo-400">{{ __('Key Configuration') }}</flux:heading>
                </div>

                {{-- Setting Key --}}
                <flux:field>
                    <flux:label badge="Required" badgeColor="text-red-600">{{ __('Setting Key') }}</flux:label>
                    <flux:input type="text" wire:model="key" :placeholder="__('e.g. ai_system_prompt')" class="font-mono" />
                    <flux:error name="key" />
                </flux:field>

                {{-- Primary Value --}}
                <flux:field>
                    <flux:label badge="Required" badgeColor="text-red-600">{{ __('Primary Value') }}</flux:label>
                    <flux:input type="text" wire:model="value" :placeholder="__('Current setting value...')" />
                    <flux:error name="value" />
                </flux:field>
 
            </div>

            {{-- Section 2: JSON Attributes / value_array --}}
            <div class="space-y-4 mt-5">
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-700 pb-2">
                    <flux:heading size="lg" class="text-indigo-600 dark:text-indigo-400">{{ __('Value Array / Attributes') }}</flux:heading>
                    <flux:button wire:click="addQuestion_attchments" variant="ghost" icon="plus" size="sm">
                        {{ __('Add Row') }}
                    </flux:button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($attributeValue_attchments as $index => $item)
                        <div wire:key="attr-{{ $index }}" class="flex items-center gap-2 group animate-in fade-in slide-in-from-left-2 duration-300">
                            <flux:input type="text" wire:model="attributeValue_attchments.{{ $index }}" 
                                :placeholder="__('Row value ' . ($index + 1))" class="flex-1" />
                            
                            @if(count($attributeValue_attchments) > 1)
                                <flux:button wire:click="removeQuestion_attchments({{ $index }})" variant="ghost" icon="trash" 
                                    class="text-zinc-400 hover:text-red-500 transition-colors" />
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Section 3: Documentation --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 mt-5">
                
                <flux:field>
                    <flux:label>{{ __('Internal Description') }}</flux:label>
                    <flux:textarea wire:model="description" :placeholder="__('What is this setting for?')" rows="3" />
                    <flux:error name="description" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Technical Notes') }}</flux:label>
                    <flux:textarea wire:model="notes" :placeholder="__('Implementation details, constraints...')" rows="3" />
                    <flux:error name="notes" />
                </flux:field>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-zinc-100 dark:border-zinc-700">
                <flux:button variant="ghost" wire:click="$refresh">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary" icon="check" class="px-8">
                    {{ __('Save Setting') }}
                </flux:button>
            </div>
        </form>
    </div>

    {{-- Settings Preview (Optional Sidebar or List) --}}
    @if($settings->isNotEmpty())
        <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl p-6 border border-dashed border-zinc-200 dark:border-zinc-800">
            <flux:heading size="lg" class="mb-4">{{ __('Recent Settings') }}</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($settings->take(4) as $s)
                    <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800">
                        <p class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mb-1 truncate">{{ $s->key }}</p>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 truncate">{{ Str::limit($s->value, 50) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
