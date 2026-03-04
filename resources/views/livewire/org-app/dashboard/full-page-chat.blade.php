<div class="flex h-[calc(100vh-64px)] bg-zinc-50 dark:bg-zinc-950 overflow-hidden">
    {{-- Custom Scrollbar Styles --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #3f3f46;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>

    {{-- Main Chat Container --}}
    <div class="flex-1 flex flex-col relative">
        {{-- Header --}}
        <header class="h-16 border-b border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md flex items-center justify-between px-6 shrink-0 z-10">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-600 rounded-lg text-white">
                    <flux:icon icon="cpu-chip" class="size-6" />
                </div>
                <div>
                    <h1 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ __('AI Copilot (Professional Mode)') }}</h1>
                    <div class="flex items-center gap-2">
                        <span class="size-2 bg-green-500 rounded-full animate-pulse"></span>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">{{ __('Connected to System Data') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <flux:button variant="subtle" size="sm" icon="arrow-path" wire:click="syncData">
                    {{ __('Sync Data') }}
                </flux:button>
            </div>
        </header>

        {{-- Messages Area --}}
        <div id="full-chat-messages" 
             class="flex-1 overflow-y-auto p-6 md:p-10 space-y-8 custom-scrollbar scroll-smooth min-h-0"
             x-ref="messagesContainer"
             x-init="$el.scrollTop = $el.scrollHeight"
             @scroll-to-bottom.window="setTimeout(() => $el.scrollTop = $el.scrollHeight, 100)">
            
            <div class="max-w-4xl mx-auto space-y-8">
                @foreach($messages as $msg)
                    <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }} animate-in fade-in slide-in-from-bottom-4 duration-500">
                        <div class="flex gap-4 max-w-[85%] {{ $msg['role'] === 'user' ? 'flex-row-reverse' : 'flex-row' }}">
                            {{-- Avatar --}}
                            <div class="shrink-0">
                                @if($msg['role'] === 'user')
                                    <flux:avatar :name="auth()->user()->name" class="size-10 shadow-sm" />
                                @else
                                    <div class="size-10 rounded-full bg-indigo-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 text-xl font-bold">
                                        ✨
                                    </div>
                                @endif
                            </div>

                            {{-- Message Content --}}
                            <div class="space-y-2">
                                <div class="px-5 py-3.5 rounded-2xl text-base leading-relaxed shadow-sm
                                    {{ $msg['role'] === 'user' 
                                        ? 'bg-indigo-600 text-white rounded-tr-none' 
                                        : 'bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-800 rounded-tl-none' }}">
                                    <div class="prose dark:prose-invert max-w-none whitespace-pre-wrap">
                                        {{ $msg['content'] }}
                                    </div>
                                </div>
                                <p class="text-[11px] font-medium opacity-50 px-2 {{ $msg['role'] === 'user' ? 'text-right' : 'text-left' }}">
                                    {{ $msg['time'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($isLoading)
                    <div class="flex justify-start animate-pulse">
                        <div class="flex gap-4">
                            <div class="size-10 rounded-full bg-indigo-100 dark:bg-zinc-800 animate-pulse"></div>
                            <div class="bg-indigo-50 dark:bg-zinc-900/50 border border-indigo-100 dark:border-zinc-800 rounded-2xl rounded-tl-none px-6 py-4 shadow-sm">
                                <div class="flex items-center gap-4">
                                    <svg class="animate-spin h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <div class="space-y-2">
                                        <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ __('AI is processing...') }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Analyzing system data and records...') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Input Area --}}
        <div class="p-6 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0">
            <div class="max-w-4xl mx-auto">
                <form wire:submit.prevent="sendMessage" class="relative group">
                    <div class="relative flex items-end gap-3 p-2 pl-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 transition-all shadow-sm">
                        <textarea 
                            wire:model="message"
                            placeholder="{{ __('Ask me anything about your organization data...') }}"
                            rows="1"
                            x-data="{ 
                                resize() { 
                                    $el.style.height = '0px'; 
                                    $el.style.height = Math.min($el.scrollHeight, 200) + 'px' 
                                } 
                            }"
                            x-init="resize()"
                            @input="resize()"
                            @keydown.enter.prevent="if (!$event.shiftKey) { $wire.sendMessage(); $el.style.height = 'auto'; } else { $event.target.value += '\n'; resize(); }"
                            class="flex-1 py-3 bg-transparent border-none text-zinc-900 dark:text-zinc-100 placeholder-zinc-500 focus:ring-0 resize-none min-h-[48px] max-h-[200px] text-base"
                            @if($isLoading) disabled @endif></textarea>

                        <button type="submit" 
                                class="p-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-600/20 disabled:opacity-50 disabled:shadow-none mb-1 group-hover:scale-105"
                                @if($isLoading || empty(trim($message))) disabled @endif>
                            <flux:icon icon="paper-airplane" class="size-6" />
                        </button>
                    </div>
                    
                    {{-- Hint --}}
                    <div class="flex justify-between items-center mt-3 px-2">
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 flex items-center gap-1.5 font-medium">
                            <flux:icon icon="information-circle" class="size-3.5" />
                            <span>Press <strong>Enter</strong> to send, <strong>Shift + Enter</strong> for new line</span>
                        </p>
                        <p class="text-[11px] text-zinc-400 dark:text-zinc-500">
                            {{ __('AI model: Gemini 2.0 Flash') }}
                        </p>
                    </div>

                    {{-- Response trigger --}}
                    <div x-init="$watch('$wire.isLoading', value => { if(value) $wire.getResponse() })"></div>
                </form>
            </div>
        </div>
    </div>

    {{-- Side Suggestions Panel (Desktop only) --}}
    <aside class="hidden lg:flex w-40 border-l border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex-col shrink-0">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
            <h2 class="font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                <flux:icon icon="light-bulb" class="size-5 text-amber-500" />
                {{ __('Suggested Questions') }}
            </h2>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
            @php
                $suggestions = [
                    ['title' => __('System Statistics'), 'q' => 'أعطني إحصائيات شاملة عن النظام اليوم'],
                    ['title' => __('Latest Activities'), 'q' => 'ما هي آخر الفعاليات التي تمت إضافتها؟'],
                    ['title' => __('Employees'), 'q' => 'هل يمكنك تزويدي بمعلومات عن الموظفين الجدد؟'],
                    ['title' => __('Feedback'), 'q' => 'ما هي آخر تقييمات الطلاب التي وصلتنا؟'],
                    ['title' => __('Budget'), 'q' => 'كيف حال الميزانية الإجمالية حالياً؟'],
                    ['title' => __('Events'), 'q' => 'ما هي المهام القادمة المجدولة؟'],
                ];
            @endphp

            @foreach($suggestions as $s)
                <button type="button" 
                        wire:click="$set('message', '{{ $s['q'] }}')"
                        class="w-full text-right p-3 rounded-xl border border-zinc-100 dark:border-zinc-800 hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition-all group">
                    <p class="text-[10px] uppercase tracking-wider text-zinc-400 font-bold mb-1">{{ $s['title'] }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        {{ $s['q'] }}
                    </p>
                </button>
            @endforeach
        </div>

        <div class="p-6 bg-zinc-50 dark:bg-zinc-950/50">
            <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Privacy Note') }}</h4>
                <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-normal">
                    {{ __('Your conversation is secure and strictly limited to your organization internal data.') }}
                </p>
            </div>
        </div>
    </aside>
</div>
