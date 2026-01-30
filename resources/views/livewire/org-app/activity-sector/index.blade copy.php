<flux:container>
    <div class="space-y-12 py-10">
        {{-- Page Header --}}
        <header class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-3">

                    <flux:heading size="xl" level="1" class="!font-black tracking-tight">Sector Strategy
                        <flux:badge color="light-blue" variant="solid" size="sm" class="!rounded-xl px-4 py-0">
                            <span class="text-lg font-black mx-1">{{ $sectors->count() }}</span> Sectors
                        </flux:badge>
                    </flux:heading>
                </div>
                <flux:subheading class="max-w-xl">Deep dive into organizational sectors, monitoring real-time
                    performance and task distribution.</flux:subheading>
            </div>

        </header>

        <flux:separator variant="subtle" />

        {{-- Parent-Child Content --}}
        <div class="space-y-20">
            @foreach ($sectors as $sector)
                @php
                    try {
                        $sectorDate = \Carbon\Carbon::createFromFormat('m/Y', $sector->activites_date);
                    } catch (\Exception $e) {
                        try {
                            $sectorDate = \Carbon\Carbon::parse($sector->activites_date);
                        } catch (\Exception $e) {
                            $sectorDate = \Carbon\Carbon::now();
                        }
                    }
                    $groupKey = $sector->sector_id . '_' . $sectorDate->format('m_Y');
                    $activities = $groupedActivities->get($groupKey) ?? collect();
                @endphp

                <section class="space-y-8">
                    {{-- Sector Header (Parent) --}}
                    <div
                        class="flex items-end justify-between border-l-[6px] border-indigo-600 pl-8 py-2 relative overflow-hidden">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">


                                <flux:heading level="2"
                                    class="text-3xl !font-black text-zinc-900 dark:text-white uppercase tracking-tighter">
                                    {{ $sector->sector_name ?? 'Sector ' . $sector->sector_id }}
                                </flux:heading>
                                <div
                                    class="flex items-center gap-2 bg-zinc-100 dark:bg-zinc-800 px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-700">
                                    <flux:icon.calendar class="w-4 h-4 text-indigo-500" />
                                    <span
                                        class="text-[11px] font-bold text-zinc-600 dark:text-zinc-400">{{ $sectorDate->format('F Y') }}
                                    </span>
                                </div>

                            </div>
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 font-medium">Monitoring
                                active operations and team engagement metrics.</flux:text>
                        </div>
                        <div class="hidden lg:flex flex-col items-end">
                            <div class="flex items-baseline gap-1">
                                <span
                                    class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-br from-indigo-600 to-indigo-400">{{ $activities->count() }}</span>
                                <span class="text-sm font-bold text-indigo-400 uppercase">Live</span>
                            </div>
                            <div class="w-32 h-1 bg-zinc-100 dark:bg-zinc-800 rounded-full mt-2 overflow-hidden">
                                <div class="h-full bg-indigo-500 w-2/3"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Activity Cards (Children) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        @forelse($activities as $index=> $activity)
                            <flux:card
                                class=" !p-0 overflow-hidden !rounded-3xl border-zinc-200 dark:border-zinc-800 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 group border-b-4 border-b-transparent hover:border-b-indigo-500 flex flex-col">
                                <div class="p-6 flex-1 space-y-5">
                                    <div class="flex items-center justify-between">

                                        <div>
                                            <flux:badge variant="outline" size="sm" color="indigo"
                                                class="font-mono !text-[10px] !px-2 !py-0.5 !rounded-lg border-indigo-200 dark:border-indigo-800 py-2 mb-2" >
                                                {{ $index + 1 }} / </flux:badge>
                                            <flux:badge variant="outline" size="sm" color="indigo"
                                                class="font-mono !text-[10px] !px-2 !py-0.5 !rounded-lg border-indigo-200 dark:border-indigo-800">
                                                ACTIVITY-NAME : {{ $activity->name }} &nbsp;
                                                <flux:icon.calendar class="w-4 h-4 text-indigo-500" />
                                                {{ \Carbon\Carbon::parse($activity->start_date)->format('M d, Y') }}

                                        </div>
                                        </flux:badge>


                                        <span @class([
                                            'text-sm max-w-xs  truncate',
                                            'text-green-600 dark:text-green-400' => $activity->status === 27,
                                            'text-yellow-600 dark:text-yellow-400' => $activity->status === 26,
                                            'text-purple-600 dark:text-purple-400' => $activity->status === 25,
                                            'text-red-600 dark:text-red-400' => $activity->status === 28,
                                        ])>
                                            {{ $activity->status_name ?? ($activity->activityStatus->status_name ?? '-') }}
                                        </span>

                                    </div>
                                    <div>
                                        @foreach ($activity->parcels as $parcel)
                                            <flux:badge variant="solid" size="sm" color=""
                                                class="font-mono !text-[10px] !px-2 !py-0.5 !rounded-lg bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-800 dark:text-purple-200 dark:border-purple-700">
                                                PARCEL : {{ $parcel->parcelType->status_name }} </flux:badge>
                                        @endforeach

                                    </div>
                                    <div class="space-y-2">
                                        <flux:heading size="md"
                                            class="group-hover:text-indigo-600 transition-colors !font-bold leading-tight">
                                            {{ $activity->regions->region_name ?? 'Not Located' }}
                                            &nbsp;/&nbsp;{{ $activity->cities->city_name ?? '' }} &nbsp;/&nbsp;
                                            {{ $activity->activityNeighbourhood->neighbourhood_name ?? '' }}
                                            &nbsp;/&nbsp; {{ $activity->activityLocation->location_name ?? '' }}
                                        </flux:heading>

                                    </div>
                                </div>

                                <div
                                    class="px-6 py-5 bg-zinc-50/50 dark:bg-zinc-800/20 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                                    <flux:button variant="subtle" size="sm" icon="eye"
                                        class="!rounded-xl !font-bold">Explore</flux:button>
                                    <div class="flex -space-x-3">
                                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=1"
                                            class="w-8 h-8 rounded-full border-2 border-white dark:border-zinc-900 shadow-sm"
                                            alt="team">
                                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=2"
                                            class="w-8 h-8 rounded-full border-2 border-white dark:border-zinc-900 shadow-sm"
                                            alt="team">
                                        <div
                                            class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 border-2 border-white dark:border-zinc-900 flex items-center justify-center text-[10px] font-black text-zinc-500 shadow-sm">
                                            +4</div>
                                    </div>
                                </div>
                            </flux:card>
                        @empty
                            <div
                                class="col-span-full py-16 flex flex-col items-center justify-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] bg-zinc-50/30 dark:bg-zinc-900/30">
                                <div
                                    class="w-16 h-16 rounded-2xl bg-white dark:bg-zinc-800 flex items-center justify-center shadow-sm border border-zinc-100 dark:border-zinc-700 mb-4">
                                    <flux:icon.archive-box variant="outline" class="w-8 h-8 text-zinc-300" />
                                </div>
                                <flux:heading size="md" class="text-zinc-500 font-bold">No Records Found
                                </flux:heading>
                                <flux:subheading size="sm" class="mt-1">This sector is waiting for initial
                                    activity deployment.</flux:subheading>
                                <flux:button variant="filled" size="sm" color="indigo" class="mt-6 !rounded-xl">
                                    Schedule Activity</flux:button>
                            </div>
                        @endforelse
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</flux:container>
