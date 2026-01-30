<flux:container>
    <div class="py-10">
        {{-- Page Header --}}
        <header class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600 p-2.5 rounded-2xl shadow-lg shadow-indigo-200 dark:shadow-none">
                        <flux:icon.briefcase class="w-6 h-6 text-white" />
                    </div>
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-3xl">Sector Strategy
                    </flux:heading>
                </div>
                <flux:subheading class="max-w-xl text-zinc-500 font-medium ml-1">Visualize organizational hierarchy and
                    operational distribution through an interactive tree view.</flux:subheading>
            </div>

            <div class="flex items-center gap-4">
                <flux:badge color="light-blue" variant="solid" size="lg"
                    class="!rounded-2xl px-5 py-1.5 shadow-sm border-0">
                    <span class="text-xl font-black mr-2">{{ $sectors->count() }}</span> <span
                        class="text-xs uppercase tracking-widest opacity-80">Sectors</span>
                </flux:badge>
            </div>
        </header>

        <div class="relative">
            {{-- Main Vertical Stem --}}
            <div
                class="absolute left-8 top-0 bottom-0 w-0.5 bg-gradient-to-b from-indigo-500 via-indigo-200 to-transparent dark:from-indigo-600 dark:via-zinc-800 dark:to-transparent opacity-50">
            </div>

            <div class="space-y-4">
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

                    <div x-data="{ open: true }" class="relative group">
                        {{-- Sector Node Indicator --}}
                        <div @click="open = !open"
                            class="absolute left-[26px] top-6 w-3 h-3 rounded-full bg-white dark:bg-zinc-900 border-2 border-indigo-600 z-10 cursor-pointer hover:scale-150 transition-transform duration-300 ring-4 ring-indigo-50 dark:ring-indigo-900/20">
                        </div>

                        {{-- Sector Content --}}
                        <div class="pl-16 pb-12">
                            {{-- Sector Header Card --}}
                            <div @click="open = !open"
                                class="cursor-pointer flex items-center justify-between bg-white dark:bg-zinc-900/50 p-6 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden group/header">
                                <div class="flex items-center gap-6">
                                    <div
                                        class="bg-indigo-50 dark:bg-indigo-900/30 p-4 rounded-2xl group-hover/header:scale-110 transition-transform">
                                        <flux:icon.folder-open class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-3">
                                            <flux:heading size="lg"
                                                class="!font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                                                {{ $sector->sector_name ?? 'Sector ' . $sector->sector_id }}
                                            </flux:heading>
                                            <flux:badge size="sm" color="zinc"
                                                class="!rounded-lg font-bold bg-zinc-100 dark:bg-zinc-800 border-0">
                                                {{ $sectorDate->format('F Y') }}
                                            </flux:badge>
                                        </div>
                                        <flux:text size="sm" class="text-zinc-500 font-medium">Monitoring
                                            {{ $activities->count() }} active operations in this sector</flux:text>
                                    </div>
                                </div>



                                {{-- Background Decorative Element --}}
                                <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl">
                                </div>
                            </div>

                            {{-- Activities Tree Branch --}}
                            <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 -translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0" class="mt-8 space-y-6 relative">
                                {{-- Inner Connecting Line --}}


                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                                    @forelse($activities as $index => $activity)
                                        <div class="relative group/activity">
                                            {{-- Horizontal Branch Line --}}
                                            <div
                                                class="absolute left-[-48px] top-1/2 w-8 h-0.5 bg-zinc-100 dark:bg-zinc-800 group-hover/activity:bg-indigo-300 transition-colors">
                                            </div>

                                         
                                                <flux:card
                                                    class=" !p-0 overflow-hidden !rounded-3xl border-zinc-200 dark:border-zinc-800 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 group border-b-4 border-b-transparent hover:border-b-indigo-500 flex flex-col">
                                                    <div class="p-6 flex-1 space-y-5">
                                                        <div class="flex items-center justify-between">

                                                            <div>
                                                                <flux:badge variant="outline" size="sm"
                                                                    color="indigo"
                                                                    class="font-mono !text-[10px] !px-2 !py-0.5 !rounded-lg border-indigo-200 dark:border-indigo-800 py-2 mb-2">
                                                                    {{ $index + 1 }} / </flux:badge>
                                                                <flux:badge variant="outline" size="sm"
                                                                    color="indigo"
                                                                    class="font-mono !text-[10px] !px-2 !py-0.5 !rounded-lg border-indigo-200 dark:border-indigo-800">
                                                                    ACTIVITY-NAME : {{ $activity->name }} &nbsp;
                                                                    <flux:icon.calendar
                                                                        class="w-4 h-4 text-indigo-500" />
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
                                                                <flux:badge variant="solid" size="sm"
                                                                    color=""
                                                                    class="font-mono !text-[10px] !px-2 !py-0.5 !rounded-lg bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-800 dark:text-purple-200 dark:border-purple-700">
                                                                    PARCEL : {{ $parcel->parcelType->status_name }}
                                                                </flux:badge>
                                                            @endforeach

                                                        </div>
                                                        <div class="space-y-2">
                                                            <flux:heading size="md"
                                                                class="group-hover:text-indigo-600 transition-colors !font-bold leading-tight">
                                                                {{ $activity->regions->region_name ?? 'Not Located' }}
                                                                &nbsp;/&nbsp;{{ $activity->cities->city_name ?? '' }}
                                                                &nbsp;/&nbsp;
                                                                {{ $activity->activityNeighbourhood->neighbourhood_name ?? '' }}
                                                                &nbsp;/&nbsp;
                                                                {{ $activity->activityLocation->location_name ?? '' }}
                                                            </flux:heading>

                                                        </div>
                                                    </div>

                                                    <div
                                                        class="px-6 py-5 bg-zinc-50/50 dark:bg-zinc-800/20 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                                                        <flux:button variant="subtle" size="sm" icon="eye"
                                                            class="!rounded-xl !font-bold">Explore</flux:button>
                                                       
                                                    </div>
                                                </flux:card>
 
                                            
                                        </div>
                                    @empty
                                        <div
                                            class="col-span-full py-12 flex flex-col items-center justify-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] bg-zinc-50/30 dark:bg-zinc-900/30">
                                            <flux:icon.archive-box variant="outline"
                                                class="w-8 h-8 text-zinc-300 mb-3" />
                                            <flux:heading size="sm" class="text-zinc-500 font-bold">No Activities
                                                Scheduled</flux:heading>
                                            <flux:button variant="subtle" size="xs" color="indigo"
                                                class="mt-4 !rounded-xl font-bold">Quick Deploy</flux:button>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</flux:container>
