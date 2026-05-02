<div x-data="activityComments"
     data-users='@json($mentionableUsers)'>

    {{-- ── Comment List ──────────────────────────────────────────────────── --}}
    <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
        @forelse($this->comments as $comment)
            <div wire:key="comment-{{ $comment->id }}" class="flex gap-3 group">
                <div class="flex-shrink-0">
                    @if($comment->creator?->google_id && $comment->creator?->avatar)
                        <img src="{{ $comment->creator->avatar }}" class="w-8 h-8 rounded-full object-cover" alt="{{ $comment->creator->name }}">
                    @else
                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-300">
                            {{ $comment->creator?->initials() ?? '?' }}
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="bg-zinc-50 dark:bg-zinc-700/50 rounded-xl rounded-tl-none px-4 py-2.5 relative">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-200">
                                {{ $comment->creator?->name ?? __('Unknown') }}
                            </span>
                            <span class="text-[10px] text-zinc-400 flex-shrink-0">
                                {{ $comment->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300 break-words">
                            {!! preg_replace('/@([\w\s]+?)(?=\s|$|@)/u',
                                '<span class="text-indigo-600 dark:text-indigo-400 font-semibold">@$1</span>',
                                e($comment->comment)) !!}
                        </p>
                        @if($comment->created_by === auth()->id())
                            <button wire:click="deleteComment({{ $comment->id }})"
                                wire:confirm="{{ __('Delete this comment?') }}"
                                class="absolute -top-2 -right-2 hidden group-hover:flex items-center justify-center w-5 h-5 rounded-full bg-red-500 text-white shadow text-[10px] hover:bg-red-600 transition">
                                ✕
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-xs text-zinc-400 py-4">{{ __('No comments yet. Be the first!') }}</p>
        @endforelse
    </div>

    {{-- ── Input ─────────────────────────────────────────────────────────── --}}
    <div class="relative">
        <div class="flex gap-2 items-end">
            <div class="flex-shrink-0 mb-1">
                @if(auth()->user()->google_id && auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" class="w-8 h-8 rounded-full object-cover" alt="">
                @else
                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-300">
                        {{ auth()->user()->initials() }}
                    </div>
                @endif
            </div>

            <div class="flex-1 relative">
                <textarea
                    x-ref="commentInput"
                    x-on:input="onInput($event)"
                    x-on:keydown.enter.prevent="submitIfNoDropdown()"
                    x-on:keydown.escape="showDropdown = false"
                    wire:model="newComment"
                    rows="2"
                    placeholder="{{ __('Write a comment… type @ to mention someone') }}"
                    class="w-full text-sm rounded-xl border border-zinc-200 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100 placeholder-zinc-400 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none transition"
                ></textarea>

                {{-- @Mention Modal (Centered) --}}
                <div x-show="showDropdown"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                     style="display:none"
                     @click.self="showDropdown = false"
                     @keydown.escape.window="showDropdown = false">
                    
                    {{-- Backdrop --}}
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

                    {{-- Modal Panel --}}
                    <div x-show="showDropdown"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                         class="relative w-full max-w-sm bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-700 flex flex-col overflow-hidden">
                        
                        {{-- Modal Header --}}
                        <div class="flex items-center justify-between px-5 py-3 border-b border-zinc-100 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">
                            <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ __('Select a team member') }}</span>
                            <button @click="showDropdown = false" type="button" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Modal Content / User List --}}
                        <div class="max-h-[50vh] overflow-y-auto py-2">
                            <template x-for="user in filteredUsers" :key="user.id">
                                <button type="button"
                                    x-on:mousedown.prevent="selectUser(user)"
                                    class="w-full flex items-center gap-3 px-5 py-3 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition text-left border-b border-zinc-50 dark:border-zinc-800/50 last:border-0">
                                    <span class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 text-sm font-bold flex items-center justify-center flex-shrink-0"
                                        x-text="user.name.charAt(0).toUpperCase()"></span>
                                    <span x-text="user.name" class="font-medium truncate"></span>
                                </button>
                            </template>
                            
                            {{-- No users found state --}}
                            <div x-show="filteredUsers.length === 0" class="px-5 py-8 text-center flex flex-col items-center gap-2">
                                <div class="w-12 h-12 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xl">🔍</div>
                                <p class="text-sm text-zinc-500">{{ __('No users found matching:') }}</p>
                                <span class="text-xs font-semibold px-2 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-md" x-text="mentionQuery"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button wire:click="addComment"
                class="flex-shrink-0 mb-1 p-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white shadow transition"
                wire:loading.attr="disabled" wire:target="addComment">
                <svg wire:loading.remove wire:target="addComment" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <svg wire:loading wire:target="addComment" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </button>
        </div>
        @error('newComment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

@script
<script>
Alpine.data('activityComments', () => ({
    allUsers: [],
    showDropdown: false,
    mentionStart: -1,
    mentionQuery: '',
    filteredUsers: [],

    init() {
        // Read users from the data attribute injected by PHP/Blade
        try {
            this.allUsers = JSON.parse(this.$el.dataset.users || '[]');
        } catch (e) {
            this.allUsers = [];
        }
    },

    onInput(event) {
        const textarea   = event.target;
        const val        = textarea.value;
        const pos        = textarea.selectionStart;
        const textBefore = val.slice(0, pos);
        const lastAt     = textBefore.lastIndexOf('@');

        if (lastAt === -1) {
            this.showDropdown = false;
            this.mentionStart = -1;
            return;
        }

        const query = textBefore.slice(lastAt + 1);

        if (query.includes(' ')) {
            this.showDropdown = false;
            this.mentionStart = -1;
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

        input.value = before + inserted + after;
        this.$wire.set('newComment', input.value);

        this.showDropdown = false;
        this.mentionStart = -1;
        input.focus();
        const newPos = (before + inserted).length;
        input.setSelectionRange(newPos, newPos);
    },

    submitIfNoDropdown() {
        if (this.showDropdown) {
            this.showDropdown = false;
        } else {
            this.$wire.call('addComment');
        }
    }
}));
</script>
@endscript
