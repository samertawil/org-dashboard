<div class="flex flex-col gap-6 print:gap-4" x-data="{ activeTab: 'overview' }">
    {{-- Header with Actions (Hidden on Print) --}}
    <div class="flex flex-col  items-start justify-between print:hidden text-left">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Activity Details') }}</flux:heading>
            <flux:subheading>{{ __('Detailed information for Activity:') }} {{ $activity->name }}</flux:subheading>
        </div>

        <div class="flex items-center gap-2 mt-3">
            <flux:button wire:click="downloadPdf" variant="outline" icon="document-arrow-down">
                {{ __('Export PDF') }}
            </flux:button>
            <flux:button onclick="window.print()" variant="ghost" icon="printer">
                {{ __('Print') }}
            </flux:button>

         

            <flux:button href="{{ route('sector.show') }}" wire:navigate variant="ghost" icon="list-bullet">
                {{ __('Sectors List') }}
            </flux:button>
        </div>
    </div>

    {{-- Print Header (Visible only on Print) --}}
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

    {{-- Tabs Navigation (Hidden on Print) --}}
    <div class="border-b border-zinc-200 dark:border-zinc-700 print:hidden text-left">
        <nav class="-mb-px flex flex-col md:flex-row md:space-x-8" aria-label="Tabs">
            <button @click="activeTab = 'overview'"
                :class="activeTab === 'overview' ? 'border-blue-600 text-blue-600 dark:text-blue-400' :
                    'border-transparent text-blue-400 hover:text-blue-600 hover:border-blue-300 dark:text-blue-400 dark:hover:text-blue-300'"
                class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer">
                <flux:icon icon="information-circle" class="mr-2 h-5 w-5"
                    x-bind:class="activeTab === 'overview' ? 'text-blue-600' : 'text-blue-400 group-hover:text-blue-600'" />
                {{ __('Overview') }}
            </button>

            <button @click="activeTab = 'location'"
                :class="activeTab === 'location' ? 'border-blue-600 text-blue-600 dark:text-blue-400' :
                    'border-transparent text-blue-400 hover:text-blue-600 hover:border-blue-300 dark:text-blue-400 dark:hover:text-blue-300'"
                class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer">
                <flux:icon icon="map-pin" class="mr-2 h-5 w-5"
                    x-bind:class="activeTab === 'location' ? 'text-blue-600' : 'text-blue-400 group-hover:text-blue-600'" />
                {{ __('Location') }}
            </button>

            <button @click="activeTab = 'financials'"
                :class="activeTab === 'financials' ? 'border-blue-600 text-blue-600 dark:text-blue-400' :
                    'border-transparent text-blue-400 hover:text-blue-600 hover:border-blue-300 dark:text-blue-400 dark:hover:text-blue-300'"
                class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer">
                <flux:icon icon="currency-dollar" class="mr-2 h-5 w-5"
                    x-bind:class="activeTab === 'financials' ? 'text-blue-600' : 'text-blue-400 group-hover:text-blue-600'" />
                {{ __('Financials') }}
            </button>

            <button @click="activeTab = 'teams'"
                :class="activeTab === 'teams' ? 'border-blue-600 text-blue-600 dark:text-blue-400' :
                    'border-transparent text-blue-400 hover:text-blue-600 hover:border-blue-300 dark:text-blue-400 dark:hover:text-blue-300'"
                class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer">
                <flux:icon icon="users" class="mr-2 h-5 w-5"
                    x-bind:class="activeTab === 'teams' ? 'text-blue-600' : 'text-blue-400 group-hover:text-blue-600'" />
                {{ __('Teams') }}
            </button>

            <button @click="activeTab = 'feedback'"
                :class="activeTab === 'feedback' ? 'border-blue-600 text-blue-600 dark:text-blue-400' :
                    'border-transparent text-blue-400 hover:text-blue-600 hover:border-blue-300 dark:text-blue-400 dark:hover:text-blue-300'"
                class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer">
                <flux:icon icon="chat-bubble-left-right" class="mr-2 h-5 w-5"
                    x-bind:class="activeTab === 'feedback' ? 'text-blue-600' : 'text-blue-400 group-hover:text-blue-600'" />
                {{ __('Feed Back') }}
            </button>
        </nav>
    </div>

    {{-- Content Area --}}
    <div class="mt-2 min-h-[400px]">

        {{-- Overview Tab --}}
        <div x-show="activeTab === 'overview'" class="space-y-6 print:block">
            <div class="flex flex-col gap-6">
                {{-- Main Info Card --}}
                
                <flux:card class="space-y-4 h-fit">
                    <flux:heading size="md" class="text-left border-b border-zinc-100 dark:border-zinc-700 pb-3">
                        {{ __('Activity Overview') }}</flux:heading>

                    <div class="flex flex-col gap-4">

                        <div class="flex justify-between items-center">
                            <span class="text-xs text-zinc-500">{{ __('Activity Name') }}</span>
                            <div class="flex items-center gap-2">

                                <span
                                    class="text-xs font-medium text-zinc-900 dark:text-zinc-100">{{ $activity->name ?? __('Unknown') }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-xs text-zinc-500">{{ __('Sector Name') }}</span>
                            <div class="flex items-center gap-2">

                                <span
                                    class="text-xs font-medium text-zinc-900 dark:text-zinc-100">{{ $activity->statusSpecificSector->status_name ?? __('Unknown') }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-xs text-zinc-500">{{ __('Duration') }}</span>
                            <div class="text-xs  ">
                                <span>{{ $activity->start_date }} -></span>

                                <span>{{ $activity->end_date ?? __('Ongoing') }}</span>
                            </div>

                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-xs text-zinc-500">{{ __('Status') }}</span>
                            <span class="text-xs text-zinc-700 dark:text-zinc-300">
                                {{ $activity->status_info['name'] }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-xs text-zinc-500">{{ __('Total Cost Dollar') }}</span>
                            <span class="text-xs text-zinc-700 dark:text-zinc-300">
                                ${{ number_format($activity->cost, 2) }} </span>
                        </div>


                        <div class="flex justify-between items-center">
                            <span class="text-xs text-zinc-500">{{ __('Total Cost Shikal') }}</span>
                            <span class="text-xs text-zinc-700 dark:text-zinc-300"> NIS &nbsp;
                                {{ number_format($activity->cost_nis, 2) }} </span>
                        </div>


                        <div class="flex justify-between items-center">
                            <span class="text-xs text-zinc-500">{{ __('Rating') }}</span>
                            <div class="flex items-center gap-1">
                                <flux:icon icon="star" variant="solid"
                                class="{{ $activity->rating_info['color'] }} w-4 h-4" />
                            <span
                                class="text-xs font-bold text-zinc-900 dark:text-zinc-100">{{ $activity->rating_info['rating'] }}</span>
                            </div>
                            
                        </div>
                        @if ($activity->description)
                        <div class="text-left pt-6 border-t border-zinc-100 dark:border-zinc-700">
                            <span class="text-xs font-medium text-zinc-500 block mb-2">{{ __('Description') }}</span>
                            <div class="prose prose-sm dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-300">
                                {{ $activity->description }}
                            </div>
                        </div>
                    @endif
                    </div>
                </flux:card>

                <flux:card class="space-y-4 h-fit">
                    <flux:heading size="md" class="text-left border-b border-zinc-100 dark:border-zinc-700 pb-3">
                        {{ __('System Metadata') }}</flux:heading>

                    <div class="flex flex-col gap-4">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-zinc-500">{{ __('Created By') }}</span>
                            <div class="flex items-center gap-2">
                                <flux:avatar src="" name="{{ $activity->creator->name ?? '?' }}"
                                    size="xs" />
                                <span
                                    class="text-xs font-medium text-zinc-900 dark:text-zinc-100">{{ $activity->creator->name ?? __('Unknown') }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-xs text-zinc-500">{{ __('System ID') }}</span>
                            <span
                                class="text-xs font-mono text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded">#PJ-{{ str_pad($activity->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-xs text-zinc-500">{{ __('Created At') }}</span>
                            <span
                                class="text-xs text-zinc-700 dark:text-zinc-300">{{ $activity->created_at->format('M d, Y H:i') }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-xs text-zinc-500">{{ __('Last Updated') }}</span>
                            <span
                                class="text-xs text-zinc-700 dark:text-zinc-300">{{ $activity->updated_at? $activity->updated_at->format('M d, Y H:i') : __('Never') }}</span>
                        </div>

                    </div>
                </flux:card>
            </div>
        </div>

        {{-- Location Tab --}}
        <div x-show="activeTab === 'location'" class="print:block print:mt-4" style="display: none;">
            <flux:card class="max-w-3xl">
                <flux:heading size="lg" class="mb-6">{{ __('Geographic Information') }}</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <span
                                class="text-xs font-medium text-zinc-500 block mb-1">{{ __('Region / Governorate') }}</span>
                            <div class="flex items-center gap-2">
                                <flux:icon icon="map" variant="outline" class="text-zinc-400 w-5 h-5" />
                                <span
                                    class="text-base text-zinc-900 dark:text-zinc-100">{{ $activity->regions->region_name ?? '-' }}</span>
                            </div>
                        </div>
                        <div>
                            <span
                                class="text-xs font-medium text-zinc-500 block mb-1">{{ __('City / Municipality') }}</span>
                            <div class="flex items-center gap-2">
                                <flux:icon icon="building-office-2" variant="outline"
                                    class="text-zinc-400 w-5 h-5" />
                                <span
                                    class="text-base text-zinc-900 dark:text-zinc-100">{{ $activity->cities->city_name ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <span class="text-xs font-medium text-zinc-500 block mb-1">{{ __('Neighborhood') }}</span>
                            <div class="flex items-center gap-2">
                                <flux:icon icon="home-modern" variant="outline" class="text-zinc-400 w-5 h-5" />
                                <span
                                    class="text-base text-zinc-900 dark:text-zinc-100">{{ $activity->activityNeighbourhood->neighbourhood_name ?? '-' }}</span>
                            </div>
                        </div>
                        <div>
                            <span
                                class="text-xs font-medium text-zinc-500 block mb-1">{{ __('Specific Location') }}</span>
                            <div class="flex items-center gap-2">
                                <flux:icon icon="map-pin" variant="outline" class="text-zinc-400 w-5 h-5" />
                                <span
                                    class="text-base text-zinc-900 dark:text-zinc-100">{{ $activity->activityLocation->location_name ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($activity->address_details)
                    <div class="mt-8 pt-6 border-t border-zinc-100 dark:border-zinc-700">
                        <span
                            class="text-xs font-medium text-zinc-500 block mb-2">{{ __('Detailed Address / Directions') }}</span>
                        <div
                            class="bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-lg border border-zinc-100 dark:border-zinc-700/50">
                            <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed">
                                {{ $activity->address_details }}</p>
                        </div>
                    </div>
                @endif
            </flux:card>
        </div>

        {{-- Financials Tab --}}
        <div x-show="activeTab === 'financials'" class="space-y-6 print:block print:mt-4" style="display: none;">

            {{-- Parcels Table --}}
            <flux:card>
                <div class="flex items-center justify-between mb-4">
                    <flux:heading size="lg">{{ __('Parcels Distribution') }}</flux:heading>
                    <flux:badge size="sm">{{ $activity->parcels->count() }} {{ __('Types') }}</flux:badge>
                </div>

                @if ($activity->parcels->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                            <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-xs uppercase font-medium text-zinc-500">
                                <tr>
                                    <th class="px-4 py-3 rounded-l-md">{{ __('Parcel Type') }}</th>
                                    <th class="px-4 py-3">{{ __('Quantity') }}</th>
                                    <th class="px-4 py-3">{{ __('Unit Cost') }}</th>
                                    <th class="px-4 py-3 rounded-r-md">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach ($activity->parcels as $parcel)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $parcel->parcelType->status_name ?? '-' }}
                                            @if ($parcel->notes)
                                                <div
                                                    class="text-xs text-zinc-400 font-normal mt-0.5 truncate max-w-xs">
                                                    {{ $parcel->notes }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">{{ $parcel->distributed_parcels_count }}</td>
                                        <td class="px-4 py-3">${{ number_format($parcel->cost_for_each_parcel, 2) }}
                                        </td>
                                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                            ${{ number_format($parcel->distributed_parcels_count * $parcel->cost_for_each_parcel, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6 text-zinc-400 italic">{{ __('No parcel data available.') }}</div>
                @endif
            </flux:card>

            {{-- Beneficiaries Table --}}
            <flux:card>
                <div class="flex items-center justify-between mb-4">
                    <flux:heading size="lg">{{ __('Beneficiaries Impact') }}</flux:heading>
                    <flux:badge size="sm" color="blue">{{ $activity->beneficiaries->count() }}
                        {{ __('Groups') }}</flux:badge>
                </div>

                @if ($activity->beneficiaries->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                            <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-xs uppercase font-medium text-zinc-500">
                                <tr>
                                    <th class="px-4 py-3 rounded-l-md">{{ __('Beneficiary Type') }}</th>
                                    <th class="px-4 py-3">{{ __('Count') }}</th>
                                    <th class="px-4 py-3">{{ __('Cost/Beneficiary') }}</th>
                                    <th class="px-4 py-3 rounded-r-md">{{ __('Est. Total') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach ($activity->beneficiaries as $beneficiary)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $beneficiary->beneficiaryType->status_name ?? '-' }}
                                            @if ($beneficiary->notes)
                                                <div
                                                    class="text-xs text-zinc-400 font-normal mt-0.5 truncate max-w-xs">
                                                    {{ $beneficiary->notes }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">{{ $beneficiary->beneficiaries_count }}</td>
                                        <td class="px-4 py-3">
                                            ${{ number_format($beneficiary->cost_for_each_beneficiary, 2) }}</td>
                                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                            ${{ number_format($beneficiary->beneficiaries_count * $beneficiary->cost_for_each_beneficiary, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6 text-zinc-400 italic">{{ __('No beneficiary data available.') }}
                    </div>
                @endif
            </flux:card>
        </div>

        {{-- Teams Tab --}}
        <div x-show="activeTab === 'teams'" class="print:block print:mt-4" style="display: none;">
            <flux:card>
                <flux:heading size="lg" class="mb-6">{{ __('Assigned Work Teams') }}</flux:heading>

                @if ($activity->workTeams->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($activity->workTeams as $team)
                            <div
                                class="flex items-start gap-3 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 hover:shadow-sm transition-shadow">
                                <flux:avatar src="" name="{{ $team->employeeRel->full_name ?? '?' }}" />
                                <div class="flex flex-col flex-1 min-w-0">
                                    <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 truncate"
                                        title="{{ $team->employeeRel->full_name ?? '-' }}">
                                        {{ $team->employeeRel->full_name ?? '-' }}
                                    </span>
                                    <span class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">
                                        {{ $team->missionTitle->status_name ?? __('Team Member') }}
                                    </span>
                                    @if ($team->notes)
                                        <p class="text-xs text-zinc-500 italic truncate" title="{{ $team->notes }}">
                                            {{ $team->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div
                            class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-zinc-100 dark:bg-zinc-800 mb-3">
                            <flux:icon icon="users" class="w-6 h-6 text-zinc-400" />
                        </div>
                        <h3 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('No teams assigned') }}
                        </h3>
                        <p class="text-xs text-zinc-500 mt-1">
                            {{ __('There are no work teams currently assigned to this activity.') }}</p>
                    </div>
                @endif
            </flux:card>
        </div>

        {{-- Feedback Tab --}}
        <div x-show="activeTab === 'feedback'" class="print:block print:mt-4" style="display: none;">
            <flux:card>
                <flux:heading size="lg" class="mb-6">{{ __('Activity Feedback') }}</flux:heading>

                @if ($activity->feedbacks->count() > 0)
                    <div class="space-y-4">
                        @foreach ($activity->feedbacks as $feedback)
                            <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-3">
                                        <flux:avatar src="" name="{{ $feedback->student->full_name ?? ($feedback->client_name ?? '?') }}" />
                                        <div>
                                            <div class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">
                                                {{ $feedback->student->full_name ?? ($feedback->client_name ?? __('Anonymous')) }}
                                            </div>
                                            <div class="text-xs text-zinc-500">
                                                {{ $feedback->created_at->format('M d, Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 bg-amber-50 dark:bg-amber-900/20 px-2 py-1 rounded-full">
                                        <flux:icon icon="star" variant="solid" class="w-4 h-4 text-amber-500" />
                                        <span class="text-sm font-bold text-amber-700 dark:text-amber-400">{{ number_format($feedback->rating, 1) }}</span>
                                    </div>
                                </div>

                                <div class="mt-3 pl-[3.25rem]">
                                    @if($feedback->feedbackTypeStatus)
                                        <div class="mb-2">
                                            <span class="inline-flex items-center rounded-md bg-zinc-100 dark:bg-zinc-800 px-2 py-1 text-xs font-medium text-zinc-600 dark:text-zinc-400 ring-1 ring-inset ring-zinc-500/10">
                                                {{ $feedback->feedbackTypeStatus->status_name }}
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed">
                                        {{ $feedback->comment }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-zinc-100 dark:bg-zinc-800 mb-3">
                            <flux:icon icon="chat-bubble-left-right" class="w-6 h-6 text-zinc-400" />
                        </div>
                        <h3 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('No feedback yet') }}</h3>
                        <p class="text-xs text-zinc-500 mt-1">{{ __('There are no feedbacks recorded for this activity.') }}</p>
                    </div>
                @endif
            </flux:card>
        </div>

    </div>

    {{-- Footer/Signature Section (Visible only on Print) --}}
    <div class="hidden print:block mt-8">
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
                margin: 0.5cm;
                size: A4 portrait;
            }

            body {
                background-color: white !important;
                color: black !important;
                font-size: 10pt;
            }

            /* Reset Layout for Print */
            [x-show],
            [style*="display: none"] {
                display: block !important;
            }

            .print\:block {
                display: block !important;
            }

            .print\:grid-cols-2 {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }

            /* Hiding Elements */
            .print\:hidden {
                display: none !important;
            }

            /* Styling Adjustments */
            .bg-white,
            .bg-zinc-50,
            .bg-zinc-800,
            .bg-zinc-900 {
                background-color: white !important;
                border: 1px solid #e5e7eb !important;
                box-shadow: none !important;
            }

            .dark\:text-white,
            .text-zinc-900,
            .dark\:text-zinc-100 {
                color: black !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid #e5e7eb;
                padding: 4px 8px;
            }

            /* Hide DebugBar */
            .phpdebugbar, .phpdebugbar-openhandler, .phpdebugbar-openhandler-overlay {
                display: none !important;
            }
        }
    </style>
</div>
