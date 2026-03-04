<div class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
    {{-- Chat Window --}}
    <div x-show="$wire.isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="mb-4 w-80 md:w-96 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden flex flex-col h-[500px] max-h-[70vh]"
         style="display: none;">
        
        {{-- Custom Scrollbar Styles --}}
        <style>
            #chat-messages::-webkit-scrollbar {
                width: 6px;
            }
            #chat-messages::-webkit-scrollbar-track {
                background: transparent;
            }
            #chat-messages::-webkit-scrollbar-thumb {
                background: #e2e8f0;
                border-radius: 10px;
            }
            .dark #chat-messages::-webkit-scrollbar-thumb {
                background: #3f3f46;
            }
            #chat-messages::-webkit-scrollbar-thumb:hover {
                background: #cbd5e1;
            }
        </style>

        {{-- Header --}}
        <div class="p-4 bg-indigo-600 dark:bg-indigo-700 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="size-8 rounded-full bg-white/20 flex items-center justify-center">
                    <flux:icon icon="cpu-chip" class="size-5" />
                </div>
                <div>
                    <h3 class="font-bold text-sm">{{ __('AI Assistant') }}</h3>
                    <p class="text-[10px] text-white/70">{{ __('Online & Ready to help') }}</p>
                </div>
            </div>
            <button wire:click="toggleChat" class="text-white/80 hover:text-white transition-colors">
                <flux:icon icon="x-mark" class="size-5" />
            </button>
        </div>

        {{-- Messages Area --}}
        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-zinc-50 dark:bg-zinc-950/50 scroll-smooth min-h-0" 
             x-ref="messagesContainer"
             x-init="$el.scrollTop = $el.scrollHeight"
             @scroll-to-bottom.window="setTimeout(() => $el.scrollTop = $el.scrollHeight, 100)">
            
            @foreach($messages as $msg)
                <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] rounded-2xl px-4 py-2 text-sm {{ $msg['role'] === 'user' ? 'bg-indigo-600 text-white rounded-tr-none shadow-md' : 'bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-tl-none shadow-sm' }}">
                        <p class="leading-relaxed whitespace-pre-wrap">{{ $msg['content'] }}</p>
                        <p class="text-[10px] mt-1 opacity-70 {{ $msg['role'] === 'user' ? 'text-right' : 'text-left' }}">{{ $msg['time'] }}</p>
                    </div>
                </div>
            @endforeach

            @if($isLoading)
                <div class="flex justify-start items-center gap-2">
                    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl rounded-tl-none px-4 py-3 shadow-sm">
                        <div class="flex items-center gap-3">
                            <svg class="animate-spin h-5 w-5 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">{{ __('Searching for answer...') }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Input Area --}}
        <form wire:submit.prevent="sendMessage" class="p-4 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800">
            <div class="relative flex items-end gap-2">
                <div class="flex-1">
                    <textarea 
                        wire:model="message"
                        placeholder="{{ __('Type your message...') }}"
                        rows="1"
                        x-data="{ 
                            resize() { 
                                $el.style.height = '0px'; 
                                $el.style.height = Math.min($el.scrollHeight, 120) + 'px' 
                            } 
                        }"
                        x-init="resize()"
                        @input="resize()"
                        @keydown.enter.prevent="if (!$event.shiftKey) { $wire.sendMessage(); $el.style.height = 'auto'; } else { $event.target.value += '\n'; resize(); }"
                        class="w-full pl-4 pr-4 py-2.5 bg-zinc-100 dark:bg-zinc-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 dark:text-zinc-100 resize-none min-h-[42px] max-h-[120px] transition-all"
                        @if($isLoading) disabled @endif></textarea>
                </div>
                
                <button type="submit" 
                        class="p-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors disabled:opacity-50 shrink-0 mb-[2px]"
                        @if($isLoading || empty(trim($message))) disabled @endif>
                    <flux:icon icon="paper-airplane" class="size-5" />
                </button>
            </div>
            
            {{-- This invisible button triggers the AI response extraction after the user message is added --}}
            <div x-init="$watch('$wire.isLoading', value => { if(value) $wire.getResponse() })"></div>
        </form>
    </div>

    {{-- Toggle Button --}}
    <button wire:click="toggleChat" 
            class="size-14 rounded-full bg-indigo-600 text-white shadow-lg shadow-indigo-500/30 flex items-center justify-center hover:bg-indigo-700 hover:scale-110 transition-all duration-300 group">
        <div class="relative">
            <flux:icon icon="cpu-chip" class="size-7 group-hover:rotate-12 transition-transform" x-show="!$wire.isOpen" />
            <flux:icon icon="x-mark" class="size-7 group-hover:rotate-90 transition-transform" x-show="$wire.isOpen" style="display: none;" />
        </div>
        
        {{-- Notification badge if closed and maybe had a new message --}}
        @if(!$isOpen && empty($messages))
            <span class="absolute top-0 right-0 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
            </span>
        @endif
    </button>
</div>
