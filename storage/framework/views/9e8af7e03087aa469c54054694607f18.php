<div x-data="{ open: false }" 
     x-init="if (<?php echo \Illuminate\Support\Js::from($this->unreadCount > 0)->toHtml() ?>) open = true"
     x-effect="if ($wire.unreadCount > 0) open = true"
     wire:poll.60s>

    
    <button @click="open = true"
        class="relative flex items-center gap-1.5 px-2 py-1.5 rounded-lg
               hover:bg-zinc-200/60 dark:hover:bg-zinc-700/60 transition focus:outline-none group">

        
        <svg  style="stroke: #FFC000" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['w-5 h-5 text-zinc-500 dark:text-zinc-400 group-hover:text-indigo-500 transition', 'animate-shake' => $this->unreadCount > 0]); ?>"
             fill="#FFC000" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002
                   6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388
                   6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0
                   11-6 0v-1m6 0H9"/>
        </svg>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->unreadCount > 0): ?>
            <span class="flex items-center justify-center min-w-[20px] h-5 px-1 rounded-full
                         bg-red-500 text-white text-[10px] font-bold leading-none
                         ring-2 ring-zinc-50 dark:ring-zinc-900">
                <?php echo e($this->unreadCount > 99 ? '99+' : $this->unreadCount); ?>

            </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </button>

    
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display:none"
         @click.self="open = false"
         @keydown.escape.window="open = false">

        
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-md bg-white dark:bg-zinc-900
                    rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-700
                    flex flex-col overflow-hidden"
             style="max-height: 85vh">

            
            <div class="flex items-center justify-between px-5 py-4
                        border-b border-zinc-100 dark:border-zinc-700 flex-shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="p-1.5 rounded-lg bg-indigo-100 dark:bg-indigo-900/40">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118
                                   14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0
                                   10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0
                                   .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3
                                   3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">
                            <?php echo e(__('Notifications')); ?>

                        </h2>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->unreadCount > 0): ?>
                            <p class="text-xs text-zinc-400">
                                <?php echo e($this->unreadCount); ?> <?php echo e(__('unread')); ?>

                            </p>
                        <?php else: ?>
                            <p class="text-xs text-zinc-400"><?php echo e(__('All caught up!')); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->unreadCount > 0): ?>
                        <button wire:click="markAllRead"
                            class="text-xs font-medium text-indigo-500 hover:text-indigo-700
                                   dark:hover:text-indigo-300 transition px-2 py-1
                                   rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30">
                            <?php echo e(__('Mark all read')); ?>

                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <button @click="open = false"
                        class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-600
                               dark:hover:text-zinc-200 hover:bg-zinc-100
                               dark:hover:bg-zinc-800 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            
            <div class="overflow-y-auto flex-1 divide-y divide-zinc-50 dark:divide-zinc-800">

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php
                        $data  = $notification->data;
                        $isNew = is_null($notification->read_at);
                        $type  = $data['type'] ?? 'general';
                    ?>

                    <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('notif-{{ $notification->id }}', get_defined_vars()); ?>wire:key="notif-<?php echo e($notification->id); ?>"
                         class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                             'flex gap-4 px-5 py-4 transition cursor-pointer',
                             'hover:bg-zinc-50 dark:hover:bg-zinc-800/60',
                             'bg-indigo-50/70 dark:bg-indigo-900/10' => $isNew,
                         ]); ?>"
                         wire:click="markRead('<?php echo e($notification->id); ?>')">

                        
                        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-base shadow-sm',
                            'bg-indigo-100 dark:bg-indigo-900/40' => $type === 'mention',
                            'bg-emerald-100 dark:bg-emerald-900/40' => $type === 'activity_created',
                            'bg-zinc-100 dark:bg-zinc-800' => !in_array($type, ['mention','activity_created']),
                        ]); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'mention'): ?> 💬
                            <?php elseif($type === 'activity_created'): ?> 📋
                            <?php else: ?> 🔔
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div class="flex-1 min-w-0">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'mention'): ?>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100 leading-snug">
                                    <span class="text-indigo-600 dark:text-indigo-400 font-semibold">
                                        <?php echo e($data['mentioned_by'] ?? ''); ?>

                                    </span>
                                    <?php echo e(__('mentioned you in')); ?>

                                    <span class="font-semibold"><?php echo e($data['activity_name'] ?? ''); ?></span>
                                </p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($data['comment_text'])): ?>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-2
                                               bg-zinc-50 dark:bg-zinc-800 rounded-lg px-3 py-1.5 italic">
                                        "<?php echo e(Str::limit($data['comment_text'], 100)); ?>"
                                    </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php elseif($type === 'activity_created'): ?>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100 leading-snug">
                                    <?php echo e(__('New activity:')); ?>

                                    <span class="font-semibold"><?php echo e($data['activity_name'] ?? ''); ?></span>
                                </p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($data['message'])): ?>
                                    <p class="text-xs text-zinc-500 mt-1"><?php echo e($data['message']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100">
                                    <?php echo e($data['message'] ?? __('New notification')); ?>

                                </p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <p class="text-xs text-zinc-400 mt-1.5">
                                <?php echo e($notification->created_at->diffForHumans()); ?>

                            </p>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isNew): ?>
                            <div class="flex-shrink-0 mt-2">
                                <div class="w-2.5 h-2.5 rounded-full bg-indigo-500 ring-2 ring-white dark:ring-zinc-900"></div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="flex flex-col items-center justify-center py-16 gap-3">
                        <div class="w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-3xl">
                            🔕
                        </div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                            <?php echo e(__('No notifications yet')); ?>

                        </p>
                        <p class="text-xs text-zinc-400"><?php echo e(__('You are all caught up!')); ?></p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->notifications->count() > 0): ?>
                <div class="px-5 py-3 border-t border-zinc-100 dark:border-zinc-700
                            bg-zinc-50 dark:bg-zinc-900/50 flex-shrink-0 text-center">
                    <p class="text-xs text-zinc-400">
                        <?php echo e(__('Showing last')); ?> <?php echo e($this->notifications->count()); ?> <?php echo e(__('notifications')); ?>

                    </p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>

<style>
@keyframes shake {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(10deg); }
    50% { transform: rotate(-10deg); }
    75% { transform: rotate(5deg); }
}
.animate-shake {
    animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
    animation-iteration-count: 3; /* Shake 3 times when it appears/refreshes */
}
</style>
    </div>

</div>
<?php /**PATH C:\xampp\htdocs\feras\org-dashboard\resources\views/livewire/notification-bell.blade.php ENDPATH**/ ?>