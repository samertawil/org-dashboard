<div class="flex flex-col gap-8">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
            <flux:subheading>{{ __('Overview of organization performance and activities.') }}</flux:subheading>
        </div>
        
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <flux:card class="flex flex-col gap-2 !bg-sky-50 dark:!bg-sky-900/10 !border-sky-200 dark:!border-sky-800/30">
            <span class="text-sm font-medium text-sky-600 dark:text-sky-400">{{ __('Total Activities') }}</span>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-sky-700 dark:text-sky-300">{{ $activeActivitiesCount }}</span>
                <flux:icon icon="chart-bar" class="text-sky-600 bg-sky-100 dark:bg-sky-500/20 p-2 rounded-lg size-10" />
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-2 !bg-emerald-50 dark:!bg-emerald-900/10 !border-emerald-200 dark:!border-emerald-800/30">
            <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">{{ __('Total Beneficiaries') }}</span>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-emerald-700 dark:text-emerald-300">{{ number_format($totalBeneficiaries) }}</span>
                <flux:icon icon="users" class="text-emerald-600 bg-emerald-100 dark:bg-emerald-500/20 p-2 rounded-lg size-10" />
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-2 !bg-violet-50 dark:!bg-violet-900/10 !border-violet-200 dark:!border-violet-800/30">
            <span class="text-sm font-medium text-violet-600 dark:text-violet-400">{{ __('Total Budget') }}</span>
            <div class="flex flex-col items-start justify-between">
                <span class="text-2xl font-bold text-violet-700 dark:text-violet-300">{{ number_format($totalBudget) }}&nbsp;$</span>
                <span class="text-2xl font-bold text-violet-700 dark:text-violet-300"> {{ number_format($totalBudgetNis) }}&nbsp;nis</span>
            </div>
           
        </flux:card>

        <flux:card class="flex flex-col gap-2 !bg-amber-50 dark:!bg-amber-900/10 !border-amber-200 dark:!border-amber-800/30">
            <span class="text-sm font-medium text-amber-600 dark:text-amber-400">{{ __('Purchase Requests') }}</span>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-amber-700 dark:text-amber-300">{{ $pendingRequests }}</span>
                <flux:icon icon="shopping-cart" class="text-amber-600 bg-amber-100 dark:bg-amber-500/20 p-2 rounded-lg size-10" />
            </div>
        </flux:card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content Area (Charts/Tasks) --}}
        <div class="lg:col-span-2 flex flex-col gap-8">
            
             {{-- My Tasks Section --}}
             <flux:card>
                <div class="flex items-center justify-between mb-4">
                    <flux:heading size="lg">{{ __('My Pending Tasks') }}</flux:heading>
                    <flux:button href="{{ route('my.tasks') }}" wire:navigate variant="ghost" size="sm" class="text-zinc-500 hover:text-zinc-800">{{ __('View All') }}</flux:button>
                </div>
                
                @if ($myTasks->isEmpty())
                    <div class="text-center py-8 text-zinc-500">
                        <flux:icon icon="check-circle" class="mx-auto size-12 mb-2 text-zinc-300" />
                        <p>{{ __('No pending tasks.') }}</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($myTasks->take(5) as $task)
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors border border-zinc-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm truncate text-zinc-800 dark:text-zinc-100">{{ $task->event->title ?? 'Untitled Task' }}</p>
                                    <p class="text-xs text-zinc-500 mt-0.5">{{ $task->event?$task->event->start->format('M d, H:i') :'' }}</p>
                                </div>
                                <flux:badge size="sm" color="zinc" class="rounded-full px-3">{{ $task->status }}</flux:badge>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>

            {{-- Recent Activity Feed --}}
            <flux:card>
                <div class="flex items-center justify-between mb-4">
                    <flux:heading size="lg">{{ __('Planned Activities') }}</flux:heading>
                    <div class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                        <flux:icon icon="calendar" class="size-5 text-zinc-500" />
                    </div>
                </div>
                
                <div class="space-y-4">
                    @forelse($plannedActivities as $activity)
                        <div class="flex items-center gap-4 group">
                            <div class="size-10 rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/30 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                <flux:icon icon="clipboard-document-list" class="size-5" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <h4 class="text-sm font-bold text-zinc-800 dark:text-zinc-100 truncate">{{ $activity->name }}</h4>
                                    <flux:badge size="sm" color="zinc" class="ml-2">{{ $activity->created_at->diffForHumans() }}</flux:badge>
                                </div>
                                <p class="text-xs text-zinc-500 truncate mt-1 flex items-center gap-2">
                                    <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $activity->statusSpecificSector->status_name ?? 'No Sector' }}</span>
                                    <span class="size-1 rounded-full bg-zinc-300"></span>
                                    <span>{{ $activity->status_info['name'] ?? 'Status' }}</span>
                                    <span class="size-1 rounded-full bg-zinc-300"></span>
                                    <span>{{ \Carbon\Carbon::parse($activity->start_date)->format('M d, Y') }}</span>
                                </p>
                            </div>
                        </div>
                        @empty 
                        <span class="text-gray-500 placeholder-gray-500">No scheduled activities.</span>
                    @endforelse
                </div>
            </flux:card>
        </div>

        {{-- Sidebar (Charts/Actions) --}}
        <div class="flex flex-col gap-8">
            {{-- Quick Actions --}}
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Quick Actions') }}</flux:heading>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('student.create') }}" wire:navigate class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-indigo-50 hover:bg-indigo-100 transition-colors border border-indigo-100 dark:bg-indigo-900/20 dark:border-indigo-800/30 group">
                        <div class="p-2 bg-white dark:bg-indigo-900/40 rounded-lg shadow-sm group-hover:scale-110 transition-transform">
                             <flux:icon icon="academic-cap" class="size-6 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <span class="text-xs font-semibold text-indigo-700 dark:text-indigo-300">{{ __('Add Student') }}</span>
                    </a>
                    
                    <a href="{{ route('purchase_request.create') }}" wire:navigate class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-emerald-50 hover:bg-emerald-100 transition-colors border border-emerald-100 dark:bg-emerald-900/20 dark:border-emerald-800/30 group">
                         <div class="p-2 bg-white dark:bg-emerald-900/40 rounded-lg shadow-sm group-hover:scale-110 transition-transform">
                            <flux:icon icon="shopping-cart" class="size-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300">{{ __('New Purchase') }}</span>
                    </a>
                    
                    <a href="{{ route('activity.index') }}" wire:navigate class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-amber-50 hover:bg-amber-100 transition-colors border border-amber-100 dark:bg-amber-900/20 dark:border-amber-800/30 group">
                        <div class="p-2 bg-white dark:bg-amber-900/40 rounded-lg shadow-sm group-hover:scale-110 transition-transform">
                            <flux:icon icon="list-bullet" class="size-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-xs font-semibold text-amber-700 dark:text-amber-300">{{ __('List Activities') }}</span>
                    </a>
                    
                    <a href="{{ route('activity.create') }}" wire:navigate class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-rose-50 hover:bg-rose-100 transition-colors border border-rose-100 dark:bg-rose-900/20 dark:border-rose-800/30 group">
                        <div class="p-2 bg-white dark:bg-rose-900/40 rounded-lg shadow-sm group-hover:scale-110 transition-transform">
                            <flux:icon icon="clipboard-document-list" class="size-6 text-rose-600 dark:text-rose-400" />
                        </div>
                        <span class="text-xs font-semibold text-rose-700 dark:text-rose-300">{{ __('Add Activity') }}</span>
                    </a>
                </div>
            </flux:card>

             {{-- Sector Distrubution Chart --}}
             <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Activities by Sector') }}</flux:heading>
                <div class="space-y-4">
                    @foreach($this->activities as $index => $sector)
                        @php
                            $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-orange-500', 'bg-pink-500'];
                            $color = $colors[$index % count($colors)];
                            $width = ($sector['value'] / max(1, $this->activities->sum('value'))) * 100;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs mb-1.5 font-medium">
                                <span class="text-zinc-700 dark:text-zinc-300">{{ $sector['label'] }}</span>
                                <span class="text-zinc-500">{{ $sector['value'] }}</span>
                            </div>
                            <div class="w-full bg-zinc-100 dark:bg-zinc-700 rounded-full h-2.5 overflow-hidden">
                                <div class="{{ $color }} h-full rounded-full transition-all duration-500" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </flux:card>

        </div>
    </div>

    @livewire('org-app.dashboard.a-i-chatbot')
</div>
