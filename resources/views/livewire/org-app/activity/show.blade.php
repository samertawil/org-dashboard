<div class="flex flex-col gap-6 print:gap-4">
    {{-- Header with Actions (Hidden on Print) --}}
    <div class="flex items-start justify-between print:hidden">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Activity Details') }}</flux:heading>
            <flux:subheading>{{ __('Detailed information for Activity:') }} {{ $activity->name }}</flux:subheading>
        </div>

        <div class="flex items-center gap-2">
            <flux:button onclick="window.print()" variant="ghost" icon="printer">
                {{ __('Print') }}
            </flux:button>


        </div>
    </div>

    {{-- Activity Report Header (Visible only on Print) --}}
    <div class="hidden print:block border-b-2 border-zinc-800 pb-2 mb-4">
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold uppercase tracking-tight">{{ __('Activity Report') }}</h1>
                <p class="text-zinc-500 font-mono text-xs mt-0.5">Generated: {{ date('Y-m-d H:i') }}</p>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-bold">{{ config('app.name') }}</h2>
                <p class="text-zinc-500 text-xs">{{ __('Organization Dashboard') }}</p>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    {{-- <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 print:grid-cols-2 print:gap-4">

       
    </div> --}}


    {{-- Activity OverView --}}

    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6 print:p-4 print:shadow-none print:border print:border-zinc-300">
        <flux:heading size="lg"
            class="mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2 print:mb-2 print:text-base">
            {{ __('Activity Overview') }}
        </flux:heading>
        <div class="flex flex-col gap-2 print:gap-1.5 mt-2">
            <div class="flex justify-between items-start">
                <span class="text-xs font-medium text-zinc-500">{{ __('Activity Name') }} : </span>
                <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right"> {{ $activity->name }}</span>
            </div>
            <div class="flex justify-between items-start">
                <span class="text-xs font-medium text-zinc-500">{{ __('Status') }} : </span>
                {{-- <span
                    class="text-xs text-zinc-900 dark:text-zinc-100 text-right">  {{ $activity->activityStatus->status_name ?? '-' }}</span> --}}
                <flux:badge :color="$activity->status_info['color']" size="sm" inset="top bottom">
                    {{ $activity->status_info['name'] }}
                </flux:badge>
            </div>
            <div class="flex justify-between items-start">
                <span class="text-xs font-medium text-zinc-500">{{ __('Start Date') }} : </span>
                <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right"> {{ $activity->start_date }}</span>
            </div>
            <div class="flex justify-between items-start">
                <span class="text-xs font-medium text-zinc-500">{{ __('End Date') }} : </span>
                <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right">
                    {{ $activity->end_date ?? __('Ongoing') }}</span>
            </div>
            <div class="flex justify-between items-start">
                <span class="text-xs font-medium text-zinc-500">{{ __('Total Cost') }} : </span>
                <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right">
                    ${{ number_format($activity->cost, 2) }}<span
                        class="text-[10px] font-normal text-zinc-500 uppercase ml-1">USD</span></span>
            </div>
            <div class="flex justify-between items-start">
                <span class="text-xs font-medium text-zinc-500">{{ __('Rating') }} : </span>
                <div class="flex items-center gap-1 justify-end">
                    <flux:icon icon="star" variant="solid" class="{{ $activity->rating_info['color'] }} w-3 h-3" />
                    <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right">
                        {{ $activity->rating_info['rating'] }}
                        @if ($activity->rating_info['rating'] > 0)
                            <span class="text-[10px] text-zinc-500">({{ $activity->rating_info['text'] }})</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
        {{-- Description Section (Full width below cards on print) --}}
        @if ($activity->description)
            <div
                class="lg:col-span-3 print:col-span-2 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6 print:p-4 print:shadow-none print:border print:border-zinc-300 mt-2">
                <label
                    class="text-[10px] font-semibold text-zinc-500 uppercase tracking-wider">{{ __('activity Description') }}
                    : </label>
                <div
                    class="prose prose-sm dark:prose-invert max-w-none mt-1 text-zinc-800 dark:text-zinc-300 text-xs leading-relaxed">
                    {{ $activity->description }}
                </div>
            </div>
        @endif
    </div>


    {{-- Location Details Card --}}
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6 print:p-4 print:shadow-none print:border print:border-zinc-300">
        <flux:heading size="lg"
            class="mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2 print:mb-2 print:text-base">
            {{ __('Location Details') }}
        </flux:heading>

        <div class="flex flex-col gap-2 print:gap-1.5 mt-2">
            <div class="flex justify-between items-start">
                <span class="text-xs font-medium text-zinc-500">{{ __('Region') }} : </span>
                <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right">
                    {{ $activity->regions->region_name ?? '-' }}</span>
            </div>
            <div class="flex justify-between items-start">
                <span class="text-xs font-medium text-zinc-500">{{ __('City') }} : </span>
                <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right">
                    {{ $activity->cities->city_name ?? '-' }}</span>
            </div>
            <div class="flex justify-between items-start">
                <span class="text-xs font-medium text-zinc-500">{{ __('Neighbourhood') }} : </span>
                <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right">
                    {{ $activity->activityNeighbourhood->neighbourhood_name ?? '-' }}</span>
            </div>
            <div class="flex justify-between items-start">
                <span class="text-xs font-medium text-zinc-500">{{ __('Location') }} : </span>
                <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right">
                    {{ $activity->activityLocation->location_name ?? '-' }}</span>
            </div>
            @if ($activity->address_details)
                <div class="mt-1 pt-1 border-t border-zinc-50 dark:border-zinc-700 print:border-zinc-200">
                    <span
                        class="text-[10px] font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Address Details') }}
                        : </span>
                    <p class="text-xs text-zinc-800 dark:text-zinc-200 italic leading-tight">
                        {{ $activity->address_details }}</p>
                </div>
            @endif
        </div>
    </div>


    {{-- Parcels Details --}}
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6 print:p-4 print:shadow-none print:border print:border-zinc-300">
        <flux:heading size="lg"
            class="mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2 print:mb-2 print:text-base">
            {{ __('Parcels Details') }}
        </flux:heading>

        <div class="flex flex-col gap-2 print:gap-1.5 mt-2">
            <div class="flex justify-between items-start">


                @forelse ($activity->parcels as  $parcel)
                    <ul>


                        <li> <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right"> <span
                                    class="text-xs font-medium text-zinc-500">{{ __('Parcel') }}</span>
                                : {{ $parcel->parcelType->status_name ?? '-' }}</span>
                        </li>

                        <li> <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right"> <span
                                    class="text-xs font-medium text-zinc-500">{{ __('Count') }}</span>
                                : {{ $parcel->distributed_parcels_count }}</span>
                        </li>
                        <li> <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right"> <span
                                    class="text-xs font-medium text-zinc-500">{{ __('Cost each parcel') }}</span>
                                : {{ $parcel->cost_for_each_parcel }} &nbsp; USD</span>
                        </li>
                        <li> <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right"> <span
                                    class="text-xs font-medium text-zinc-500">{{ __('Notes') }}</span>
                                : {{ $parcel->notes }} </span>
                        </li>
                    </ul>



                @empty
                    <span>No Parcels Found</span>
                @endforelse

            </div>
        </div>


    </div>

    {{-- Beneficiaries Details --}}
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6 print:p-4 print:shadow-none print:border print:border-zinc-300">
        <flux:heading size="lg"
            class="mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2 print:mb-2 print:text-base">
            {{ __('Beneficiaries Details') }}
        </flux:heading>

        <div class="flex flex-col gap-2 print:gap-1.5 mt-2">
            <div class="flex justify-between items-start">


                @forelse ($activity->beneficiaries as  $beneficiary)
                    <ul>


                        <li> <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right"> <span
                                    class="text-xs font-medium text-zinc-500">{{ __('Parcel') }}</span>
                                : {{ $beneficiary->beneficiaryType->status_name ?? '-' }}</span>
                        </li>

                        <li> <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right"> <span
                                    class="text-xs font-medium text-zinc-500">{{ __('Count') }}</span>
                                : {{ $beneficiary->beneficiaries_count }}</span>
                        </li>
                        <li> <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right"> <span
                                    class="text-xs font-medium text-zinc-500">{{ __('Cost each parcel') }}</span>
                                : {{ $beneficiary->cost_for_each_beneficiary }} &nbsp; USD</span>
                        </li>
                        <li> <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right"> <span
                                    class="text-xs font-medium text-zinc-500">{{ __('Notes') }}</span>
                                : {{ $beneficiary->notes }} </span>
                        </li>
                    </ul>



                @empty
                    <span>No Beneficiaries Found</span>
                @endforelse

            </div>
        </div>


    </div>


    {{-- work teams Details --}}
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6 print:p-4 print:shadow-none print:border print:border-zinc-300">
        <flux:heading size="lg"
            class="mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2 print:mb-2 print:text-base">
            {{ __('Work Teams Details') }}
        </flux:heading>

        <div class="flex flex-col gap-2 print:gap-1.5 mt-2">
            <div class="flex justify-between items-start">


                @forelse ($activity->workTeams as  $team)
                    <ul>

               
                        <li> <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right"> <span
                                    class="text-xs font-medium text-zinc-500">{{ __('Employee') }}</span>
                                : {{ $team->employeeRel->full_name ?? '-' }}</span>
                        </li>

                        <li> <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right"> <span
                                    class="text-xs font-medium text-zinc-500">{{ __('mission Title') }}</span>
                                : {{ $team->missionTitle->status_name }}</span>
                        </li>
                        <li> <span class="text-xs text-zinc-900 dark:text-zinc-100 text-right"> <span
                                    class="text-xs font-medium text-zinc-500">{{ __('Notes') }}</span>
                                : {{ $team->notes }} </span>
                        </li>

                    </ul>



                @empty
                    <span>No Teams Found</span>
                @endforelse

            </div>
        </div>


    </div>

    {{-- Metadata (Side-by-side with description or below on print to save space) --}}
    <div
        class="hidden print:flex justify-between items-center px-4 py-2 bg-zinc-50 border border-zinc-200 rounded mt-2">
        <span class="text-[9px] text-zinc-500">{{ __('Created By:') }}
            {{ $activity->creator->name ?? __('Unknown') }}</span>
        <span class="text-[9px] text-zinc-500">{{ __('System ID:') }}
            #PJ-{{ str_pad($activity->id, 4, '0', STR_PAD_LEFT) }}</span>
        <span class="text-[9px] text-zinc-500">{{ __('Status Update:') }}
            {{ $activity->updated_at->format('Y-m-d') }}</span>
    </div>

    {{-- System Meta Card (Desktop Only) --}}
    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 print:hidden">
        <flux:heading size="md" class="mb-3">{{ __('System Metadata') }}</flux:heading>
        <div class="flex flex-col gap-2">
            <div class="text-xs text-zinc-500">
                <span class="font-medium">{{ __('Created By:') }}</span>
                {{ $activity->creator->name ?? __('Unknown') }}
            </div>
            <div class="text-xs text-zinc-500">
                <span class="font-medium">{{ __('Created At:') }}</span>
                {{ $activity->created_at->format('Y-m-d H:i') }}
            </div>
            <div class="text-xs text-zinc-500">
                <span class="font-medium">{{ __('Last Updated:') }}</span>
                {{ $activity->updated_at->format('Y-m-d H:i') }}
            </div>
        </div>
    </div>

    {{-- Footer/Signature Section (Visible only on Print) --}}
    <div class="hidden print:block mt-6">
        <div class="grid grid-cols-2 gap-8">
            <div class="text-center">
                <div class="border-t border-zinc-400 pt-1 px-4">
                    <p class="text-xs font-bold">{{ __('Activity Manager Signature') }}</p>
                    <p class="text-[10px] text-zinc-500 mt-1">{{ date('Y-m-d') }}</p>
                </div>
            </div>
            <div class="text-center">
                <div class="border-t border-zinc-400 pt-1 px-4">
                    <p class="text-xs font-bold">{{ __('Organization Approval') }}</p>
                    <p class="text-[10px] text-zinc-500 mt-1">{{ __('Stamp / Date') }}</p>
                </div>
            </div>
        </div>
        <p class="text-[8px] text-zinc-400 text-center mt-6 italic border-t border-zinc-100 pt-2">
            {{ __('This is an automated report from the Organization Dashboard System.') }}
        </p>
    </div>

    <style>
        @media print {
            @page {
                margin: 1cm;
                size: A4 portrait;
            }

            body {
                background-color: white !important;
                color: black !important;
                font-size: 11px;
            }

            .bg-white,
            .bg-zinc-50,
            .bg-zinc-800,
            .bg-zinc-900 {
                background-color: transparent !important;
            }

            .text-zinc-100,
            .text-zinc-200,
            .text-zinc-300,
            .dark\:text-white {
                color: black !important;
            }

            .border-zinc-200,
            .dark\:border-zinc-700 {
                border-color: #d1d5db !important;
            }

            /* Force grid for print */
            .print\:grid-cols-2 {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }

            .print\:col-span-1 {
                grid-column: span 1 / span 1 !important;
            }

            .print\:col-span-2 {
                grid-column: span 2 / span 2 !important;
            }
        }
    </style>
</div>
