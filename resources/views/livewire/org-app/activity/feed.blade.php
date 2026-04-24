<div class="min-h-screen bg-slate-50" x-data x-on:modal-show.window="Flux.modal($event.detail.name).show()">
    <!-- Top Navigation Custom Header (Since no sidebar) -->
    <header class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 sticky top-0 z-30 mb-8">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button href="{{ route('dashboard') }}" variant="ghost" icon="arrow-right" size="sm"
                    class="rounded-xl" wire:navigate>
                    {{ __('Back To Dashboard') }}
                </flux:button>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800"></div>
                <flux:heading size="lg" class="font-black text-indigo-600 tracking-tight  hidden md:block" >
                    {{ __('Activity Timeline') }}</flux:heading>
            </div>

            <div class="flex items-center gap-3">
                <flux:text size="sm" class="font-bold hidden sm:block">{{ auth()->user()->name }}</flux:text>
              <flux:profile
                    :avatar="auth()->user()->google_id && auth()->user()->avatar ? auth()->user()->avatar : null"
                    :initials="auth()->user()->initials()" />  
            </div>
        </div>
    </header>

    <div class="max-w-6xl mx-auto px-4 py-8">

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <!-- Left Sidebar: Filters (Facebook Style) -->
            {{-- <aside class="lg:col-span-1 space-y-6">
                <div
                    class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-sm border border-zinc-100 dark:border-zinc-800 sticky top-24">
                    <flux:heading size="lg" class="mb-6 font-black flex items-center gap-2">
                        <flux:icon icon="funnel" variant="mini" class="text-indigo-600" />
                        {{ __('تصفية النشاطات') }}
                    </flux:heading>

                    <div class="space-y-6">
                   
                        <flux:field>
                            <flux:label>{{ __('بحث عن نشاط') }}</flux:label>
                            <flux:input wire:model.live.debounce.300ms="search" :placeholder="__('اكتب اسم النشاط...')"
                                icon="magnifying-glass" class="rounded-2xl" />
                        </flux:field>

                     
                        <flux:field>
                            <flux:label>{{ __('الحالة') }}</flux:label>
                            <flux:select wire:model.live="status_id" class="rounded-2xl">
                                <option value="">{{ __('جميع الحالات') }}</option>
                                @foreach ($this->allStatuses->where('p_id_sub', config('appConstant.activity_status')) as $status)
                                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                     
                        <flux:field>
                            <flux:label>{{ __('المنطقة') }}</flux:label>
                            <flux:select wire:model.live="region_id" class="rounded-2xl">
                                <option value="">{{ __('جميع المناطق') }}</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->region_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
 
                        <flux:field>
                            <flux:label>{{ __('المدينة') }}</flux:label>
                            <flux:select wire:model.live="city_id" :disabled="!$region_id" class="rounded-2xl">
                                <option value="">{{ __('جميع المدن') }}</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        @if ($search || $status_id || $region_id || $city_id)
                            <flux:button
                                wire:click="$set('search', ''); $set('status_id', ''); $set('region_id', ''); $set('city_id', '')"
                                variant="ghost" size="sm" class="w-full rounded-xl text-rose-500 hover:bg-rose-50">
                                {{ __('إلغاء جميع الفلاتر') }}
                            </flux:button>
                        @endif
                    </div>
                </div>
            </aside> --}}

            <!-- Main Feed -->
            <main class="lg:col-span-3 space-y-6">
                {{-- Feed Header --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="size-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                            <flux:icon icon="rocket-launch" />
                        </div>
                        <div>
                            <flux:heading size="xl" class="font-black tracking-tight text-end">
                                {{ __('List of activities') }}</flux:heading>
                            <flux:text size="sm" class="text-zinc-500 text-end">
                                {{ __('Follow the progress of events and activities moment by moment') }}</flux:text>
                        </div>
                    </div>


                </div>

                {{-- Activities List --}}
                <div class="space-y-8">
                    @forelse($this->activities as $activity)
                        <article
                            class="bg-white dark:bg-zinc-900 rounded-[2.5rem] p-8 shadow-sm border border-zinc-100 dark:border-zinc-800 transition-all hover:shadow-xl group">
                            {{-- Post Header --}}
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-4">
                                    <!-- Organization Logo as Avatar -->
                                    <div
                                        class="size-12 rounded-xl bg-white dark:bg-zinc-800 flex items-center justify-center overflow-hidden border border-zinc-100 dark:border-zinc-800 shadow-sm p-1">
                                        <img src="{{ asset('logo2.png') }}"
                                            class="max-h-full max-w-full object-contain" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span
                                            class="font-black text-sm text-zinc-900 dark:text-zinc-100 leading-tight ">{{ $activity->creator->name ?? __('نظام المؤسسة') }}</span>
                                        <div class="flex items-center gap-2 text-xs text-zinc-400">
                                            <flux:icon icon="clock" size="xs" />
                                            <span>{{ $activity->created_at->diffForHumans() }}</span>

                                        </div>
                                    </div>

                                </div>

                                <div class="flex items-center gap-2">
                                    <flux:badge :color="$activity->status_info['color']" variant="solid"
                                        class="rounded-full px-4 font-bold text-xs">
                                        {{ $activity->status_info['name'] }}
                                    </flux:badge>
                                
                                </div>
                            </div>

                            {{-- Activity Content --}}
                            <div class="space-y-6">
                                <h3
                                    class="text-2xl font-black text-zinc-900 dark:text-zinc-100 tracking-tight leading-tight mb-0">
                                    {{ $activity->name }}

                                </h3>
                                <div class="flex">

                                    <flux:subheading size="sm" class="text-zinc-500">
                                        {{ $activity->statusSpecificSector->status_name }}&nbsp;&nbsp;
                                    </flux:subheading>
 
                                    <div class="flex items-center gap-1">
                                        <flux:icon icon="star" variant="solid"
                                            class="{{ $activity->rating_info['color'] }} w-4 h-4" />
                                            @if ($activity->rating_info['rating']>0)
                                            <span
                                            class="text-xs font-bold text-zinc-900 dark:text-zinc-100">{{ $activity->rating_info['rating'] }}</span> 
                                            @endif
                                      
                                    </div>
                                </div>




                                <div
                                    class="bg-zinc-50 dark:bg-zinc-800/50 rounded-[2rem] p-6 border border-zinc-100 dark:border-zinc-800/50">
                                    @if ($activity->description)
                                        <p class="text-zinc-700 dark:text-zinc-300 leading-relaxed text-xl mb-6">
                                            {{ $activity->description }}
                                        </p>
                                    @endif

                                    {{-- Structured Post Data (Facebook style summary) --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                        {{-- Financials --}}
                                        @if ($activity->cost > 0 || $activity->cost_nis > 0)
                                            <div
                                                class="flex items-center gap-2 text-emerald-700 dark:text-emerald-400 font-bold bg-white dark:bg-zinc-900/60 px-4 py-2 rounded-2xl shadow-sm border border-emerald-50 content-center">
                                                <flux:icon icon="currency-dollar" size="sm" />
                                                <span>
                                                    @if ($activity->cost > 0)
                                                        ${{ number_format($activity->cost, 2) }}
                                                    @endif
                                                    @if ($activity->cost > 0 && $activity->cost_nis > 0)
                                                        /
                                                    @endif
                                                    @if ($activity->cost_nis > 0)
                                                        {{ number_format($activity->cost_nis, 2) }} ₪
                                                    @endif
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Beneficiaries --}}
                                        @foreach ($activity->beneficiaries as $beneficiary)
                                            <div
                                                class="flex items-center gap-2 text-indigo-700 dark:text-indigo-400 font-bold bg-white dark:bg-zinc-900/60 px-4 py-2 rounded-2xl shadow-sm border border-indigo-50">
                                                <flux:icon icon="users" size="sm" />
                                                <span>{{ $beneficiary->beneficiaries_count }}
                                                    {{ $beneficiary->beneficiaryType->status_name }}</span>
                                            </div>
                                        @endforeach

                                        {{-- Parcels --}}
                                        @foreach ($activity->parcels as $parcel)
                                            <div
                                                class="flex items-center gap-2 text-amber-700 dark:text-amber-400 font-bold bg-white dark:bg-zinc-900/60 px-4 py-2 rounded-2xl shadow-sm border border-amber-50">
                                                <flux:icon icon="archive-box" size="sm" />
                                                <span>{{ $parcel->distributed_parcels_count }}
                                                    {{ $parcel->parcelType->status_name }}
                                                    ({{ $parcel->unit->status_name }})
                                                </span>
                                            </div>
                                        @endforeach

                                        {{-- Beneficiary Names --}}
                                        @if ($activity->beneficiary_names_count > 0)
                                            <button wire:click="showBeneficiaries({{ $activity->id }})"
                                                class="flex items-center gap-2 text-rose-700 dark:text-rose-400 font-bold bg-white dark:bg-zinc-900/60 px-4 py-2 rounded-2xl shadow-sm border border-rose-50 hover:bg-rose-50 transition-colors cursor-pointer">
                                                <flux:icon icon="user-group" size="sm" />
                                                <span>{{ $activity->beneficiary_names_count }}
                                                    {{ __('Beneficiary Names') }}</span>
                                            </button>
                                        @endif

                                        {{-- Purchase Requisitions --}}
                                        @php
                                            $prs = $activity->parcels->map(fn($p) => $p->purchaseRequisition)->filter()->unique('id');
                                        @endphp
                                        @foreach ($prs as $pr)
                                            <button wire:click="showDetails({{ $pr->id }})"
                                                class="flex items-center gap-2 text-blue-700 dark:text-blue-400 font-bold bg-white dark:bg-zinc-900/60 px-4 py-2 rounded-2xl shadow-sm border border-blue-50 hover:bg-blue-50 transition-colors cursor-pointer">
                                                <flux:icon icon="document-text" size="sm" />
                                                <span>{{ __('PR') }} #{{ $pr->request_number }}</span>
                                            </button>
                                        @endforeach
                                    </div>

                                    {{-- Team Tags (Facebook Style "With") --}}
                                    @if ($activity->workTeams->isNotEmpty())
                                        <div
                                            class="mt-6 flex flex-wrap items-center gap-2 border-t border-zinc-200 dark:border-zinc-700 pt-4">
                                            <span class="text-xs font-bold text-zinc-400">
                                                {{ __(' فريق العمل:') }}</span>
                                            @foreach ($activity->workTeams as $member)
                                                <flux:badge icon="user" size="sm" variant="ghost"
                                                    class="rounded-xl">{{ $member->employeeRel->full_name ?? '-' }}
                                                </flux:badge>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Attachments Gallery (Always below the content box) --}}
                            @if ($activity->attachments->isNotEmpty())
                                <div
                                    class="mt-6 relative rounded-[2rem] overflow-hidden group/gallery border border-zinc-100 dark:border-zinc-800">
                                    <div class="grid grid-cols-2 gap-2 max-h-[400px]">
                                        @foreach ($activity->attachments->take(3) as $index => $attachment)
                                            <div @class([
                                                'relative bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center overflow-hidden',
                                                'col-span-2' => $activity->attachments->count() === 1,
                                                'row-span-2 h-full' => $activity->attachments->count() > 1 && $index === 0,
                                            ])>
                                                @php
                                                    $path = $attachment->attchment_path;
                                                    $isImage = in_array(pathinfo($path, PATHINFO_EXTENSION), [
                                                        'jpg',
                                                        'jpeg',
                                                        'png',
                                                        'webp',
                                                        'gif',
                                                    ]);
                                                @endphp

                                                @if ($isImage)
                                                    <img src="{{ Storage::url($path) }}"
                                                        class="w-full h-full object-cover transition-transform duration-700 group-hover/gallery:scale-110" />
                                                @else
                                                    <div class="flex flex-col items-center gap-2 p-8 text-zinc-400">
                                                        <flux:icon icon="document-text" size="lg" />
                                                        <span
                                                            class="text-xs font-bold">{{ strtoupper(pathinfo($path, PATHINFO_EXTENSION)) }}</span>
                                                    </div>
                                                @endif

                                                @if ($index === 2 && $activity->attachments->count() > 3)
                                                    <div
                                                        class="absolute inset-0 bg-black/60 flex items-center justify-center text-white z-10">
                                                        <span
                                                            class="text-2xl font-black">+{{ $activity->attachments->count() - 3 }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Metadata Row --}}
                            <div
                                class="flex flex-wrap  items-center gap-6 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                                <div class="flex items-center gap-2 text-sm text-zinc-500">

                                    <svg viewBox="0 0 24 24" class="size-5 shrink-0"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"
                                            fill="#EA4335" />
                                    </svg>
                                    <span class="font-bold">{{ $activity->regions->region_name ?? '-' }}</span>
                                    <span class="text-zinc-300 mx-1">/</span>
                                    <span
                                        class="font-bold text-zinc-400">{{ $activity->cities->city_name ?? '-' }}</span>
                                </div>

                                <div class="flex items-center gap-2 text-sm text-zinc-500">
                                    {{-- <flux:icon icon="calendar-days" size="sm" class="text-emerald-500" /> --}}
                                    <span class="font-bold">{{ $activity->start_date }}</span>
                                    @if ($activity->end_date && $activity->end_date != $activity->start_date)
                                        <flux:icon icon="arrow-long-left" size="xs" class="text-zinc-300" />
                                        <span class="font-bold text-zinc-400">{{ $activity->end_date }}</span>
                                    @endif
                                </div>



                                <div class="flex-1 flex justify-end gap-2">
                                    <flux:button variant="ghost" size="sm"
                                        class="rounded-xl hover:bg-indigo-50 hover:text-indigo-600"
                                        icon="chat-bubble-left-ellipsis">
                                        {{ $activity->comments->count() }} {{ __('Comments') }}
                                    </flux:button>
                                    <flux:button variant="ghost" size="sm"
                                        class="rounded-xl hover:bg-blue-50 hover:text-blue-600" icon="share">
                                        {{ __('مشاركة') }}
                                    </flux:button>
                                        
                                    @can('activity.create')
                                        <flux:button href="{{ route('activity.edit', $activity) }}" wire:navigate variant="ghost" size="sm" icon="pencil-square" inset="right" class="rounded-xl text-zinc-400 hover:text-indigo-600 transition-colors" />
                                    @endcan
                                </div>
                            </div>

                            {{-- Comments Section (Facebook Style) --}}
                            <div class="mt-1 pt-1 border-t border-zinc-50 dark:border-zinc-800/50">
                                {{-- Existing Comments --}}
                                <div class="space-y-4 mb-6">
                                    @foreach ($activity->comments as $comment)
                                        <div class="flex gap-3 group/comment">
                                            <div class="size-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[10px] font-black text-zinc-400 shrink-0 border border-zinc-200 dark:border-zinc-700">
                                                {{ Str::limit($comment->creator->name ?? '?', 1, '') }}
                                            </div>
                                            <div class="flex-1">
                                                <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl px-4 py-2 inline-block max-w-full">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="text-xs font-black text-zinc-900 dark:text-zinc-100">{{ $comment->creator->name ?? '-' }}</span>
                                                        <span class="text-[10px] text-zinc-400 font-medium">{{ $comment->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-normal">
                                                        {{ $comment->comment }}
                                                    </p>
                                                </div>
                                                
                                                @if($comment->created_by === auth()->id() || auth()->user()->can('activity.create'))
                                                    <button wire:click="deleteComment({{ $comment->id }})" wire:confirm="{{ __('Are you sure?') }}" class="block mt-1 text-[10px] font-bold text-rose-500 opacity-0 group-hover/comment:opacity-100 transition-opacity px-2">
                                                        {{ __('Delete') }}
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- New Comment Input --}}
                                <div class="flex gap-3 items-start">
                                    <div class="size-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/40 flex items-center justify-center text-[10px] font-black text-indigo-600 shrink-0 border border-indigo-100 dark:border-indigo-900/30">
                                        {{ Str::limit(auth()->user()->name, 1, '') }}
                                    </div>
                                    <div class="flex-1 relative">
                                        <form wire:submit.prevent="addComment({{ $activity->id }})">
                                            <input 
                                                type="text" 
                                                wire:model="newCommentText.{{ $activity->id }}"
                                                placeholder="{{ __('Write a comment...') }}"
                                                class="w-full bg-zinc-100 dark:bg-zinc-800 border-none rounded-2xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500/20 dark:text-zinc-200 transition-all placeholder:text-zinc-400"
                                            />
                                            <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-indigo-600 hover:text-indigo-700 transition-colors">
                                                <flux:icon icon="paper-airplane" size="xs" variant="mini" />
                                            </button>
                                        </form>
                                        @error('newCommentText.' . $activity->id)
                                            <span class="text-[10px] font-bold text-rose-500 mt-1 block px-2">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div
                            class="py-24 text-center bg-white dark:bg-zinc-900 rounded-[2.5rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                            <flux:icon icon="magnifying-glass" size="lg" class="mx-auto text-zinc-300 mb-4" />
                            <h4 class="text-xl font-bold text-zinc-600">{{ __('لم يتم العثور على أي نشاطات') }}</h4>
                            <p class="text-zinc-400 mt-2">{{ __('حاول تغيير خيارات البحث أو الفلاتر') }}</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-12">
                    {{ $this->activities->links() }}
                </div>
            </main>
        </div>
    {{-- Beneficiaries Modal --}}
    <flux:modal name="beneficiaries-modal" class="md:w-[900px]">
        <div class="flex flex-col gap-6 text-left">
            @if ($selectedActivityForBeneficiaries)
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <flux:heading level="2" size="lg">{{ __('Beneficiaries') }}</flux:heading>
                        <flux:subheading>{{ $selectedActivityForBeneficiaries->name }}</flux:subheading>
                    </div>

                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <flux:input wire:model.live.debounce.300ms="beneficiarySearch" :placeholder="__('Search by name...')"
                            icon="magnifying-glass" size="sm" class="w-full md:w-64" />

                        <flux:button wire:click="exportBeneficiaries({{ $selectedActivityForBeneficiaries->id }})"
                            variant="ghost" icon="document-arrow-down" class="text-green-600" size="sm">
                            {{ __('Export') }}
                        </flux:button>
                    </div>
                </div>

                <div class="overflow-hidden border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                            <tr>
                                <th class="px-4 py-2 text-left">{{ __('Full Name') }}</th>
                                <th class="px-4 py-2 text-left">{{ __('Receipt Date') }}</th>
                               
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse($this->selectedActivityBeneficiaries as $beneficiary)
                                <tr>
                                    <td class="px-4 py-2 font-medium text-zinc-900 dark:text-white">
                                        {{ $beneficiary->full_name }}</td>
                                    <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ $beneficiary->receipt_date }}<br>
                                     {{ $beneficiary->status->status_name ?? $beneficiary->receive_method }}
                                    </td>
                                     
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-zinc-500">
                                        {{ __('No beneficiaries found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </flux:modal>
    {{-- PR Details Modal --}}
    <flux:modal name="show-pr-modal" class="md:w-[800px]">
        <div class="flex flex-col gap-6 text-left">
            @if ($selectedPr)
                <div class="flex justify-between items-center">
                    <flux:heading level="2" size="lg">{{ __('Purchase Requisition') }}
                        #{{ $selectedPr->request_number }}</flux:heading>
                    <flux:button href="{{ route('purchase_request.show', $selectedPr->id) }}" variant="ghost"
                        icon="printer" tooltip="{{ __('Print / Full View') }}">
                        {{ __('Print') }}
                    </flux:button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <flux:label>{{ __('Request Date') }}</flux:label>
                            <div class="text-zinc-800 dark:text-zinc-200">
                                {{ $selectedPr->request_date ? $selectedPr->request_date->format('Y-m-d') : '-' }}</div>
                        </div>
                        <div>
                            <flux:label>{{ __('Status') }}</flux:label>
                            <div><flux:badge>{{ $selectedPr->status->status_name ?? '-' }}</flux:badge></div>
                        </div>
                        @if ($selectedPr->order_count)
                            <div>
                                <flux:label>{{ __('Order Count') }}</flux:label>
                                <div class="text-zinc-800 dark:text-zinc-200">{{ $selectedPr->order_count }}</div>
                            </div>
                        @endif
                        <div>
                            <flux:label>{{ __('Requested By') }}</flux:label>
                            <div class="text-zinc-800 dark:text-zinc-200">{{ $selectedPr->creator->name ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <flux:label>{{ __('Estimated Total (Dollar)') }}</flux:label>
                            <div class="text-zinc-800 dark:text-zinc-200 font-semibold">
                                ${{ number_format($selectedPr->estimated_total_dollar, 2) }}</div>
                        </div>
                        <div>
                            <flux:label>{{ __('Estimated Total (NIS)') }}</flux:label>
                            <div class="text-zinc-800 dark:text-zinc-200 font-semibold">
                                ₪{{ number_format($selectedPr->estimated_total_nis, 2) }}</div>
                        </div>
                        <div>
                            <flux:label>{{ __('Quotation Deadline') }}</flux:label>
                            <div class="text-zinc-800 dark:text-zinc-200">
                                {{ $selectedPr->quotation_deadline ? $selectedPr->quotation_deadline->format('Y-m-d') : '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <flux:label>{{ __('Suggested Vendors') }}</flux:label>
                    <div class="flex flex-wrap gap-1">
                        @foreach ($selectedPr->suggested_vendors as $vendor)
                            <flux:badge size="sm" variant="outline">{{ $vendor->name }}</flux:badge>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-2">
                    <flux:label>{{ __('Description') }}</flux:label>
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded border border-zinc-200 dark:border-zinc-700">
                        {{ $selectedPr->description ?? '-' }}
                    </div>
                </div>

                @if ($selectedPr->justification)
                    <div class="space-y-2">
                        <flux:label>{{ __('Justification') }}</flux:label>
                        <div
                            class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded border border-zinc-200 dark:border-zinc-700 italic">
                            {{ $selectedPr->justification }}
                        </div>
                    </div>
                @endif

                <div class="space-y-2">
                    <flux:heading level="3" size="md">{{ __('Items') }}</flux:heading>
                    <div class="overflow-hidden border border-zinc-200 dark:border-zinc-700 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                                <tr>
                                    <th class="px-3 py-2 text-left">{{ __('Item') }}</th>
                                    <th class="px-3 py-2 text-center">{{ __('Qty') }}</th>
                                    <th class="px-3 py-2 text-left">{{ __('Unit') }}</th>
                                    <th class="px-3 py-2 text-right">{{ __('Price') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach ($selectedPr->items as $item)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <div class="font-medium">{{ $item->item_name }}</div>
                                            <div class="text-xs text-zinc-500">{{ $item->item_description }}</div>
                                        </td>
                                        <td class="px-3 py-2 text-center">{{ $item->quantity }}</td>
                                        <td class="px-3 py-2 text-left">{{ $item->unit->status_name ?? '-' }}</td>
                                        <td class="px-3 py-2 text-right font-mono">{{ number_format($item->unit_price, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </flux:modal>
    </div>
</div>


