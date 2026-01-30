<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ $subheading ?? __('Enter the details for the Activity below.') }}</flux:subheading>
        </div>

        <flux:button href="{{ route('activity.index') }}" wire:navigate variant="ghost" icon="list-bullet">
            {{ __('Activity List') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center {{ session('type') == 'error' ? 'text-red-500' : '' }}" :status="session('message')" />

    {{-- Form Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <form wire:submit="{{ $type }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- Basic Information Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-2">
                <flux:heading size="lg">{{ __('Basic Information') }}</flux:heading>
            </div>

            {{-- activity Name --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('activity Name') }}</flux:label>
                <flux:input type="text" wire:model="name" :placeholder="__('Enter Activity name')" />
                <flux:error name="name" />
            </flux:field>

              {{-- Sector --}}
              <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">Sector</flux:label>
                <flux:select wire:model.live.lazy="sector_id">
                    <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Specific Sector') }}</option>
                    @foreach ($this->allStatuses->where('p_id_sub',config('appConstant.sectors')) as $sector)
                        <option value="{{ $sector->id }}">{{ $sector->status_name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="sector_id" />
            </flux:field>

            {{-- Cost --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Tottal Cost USD') }}</flux:label>
                <flux:input type="number" step="0.01" wire:model="cost" :placeholder="0.0" />
                <flux:error name="cost" />
            </flux:field>

            {{-- Start Date --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Start Date') }}</flux:label>
                <flux:input type="date" wire:model="start_date" />
                <flux:error name="start_date" />
            </flux:field>

            {{-- End Date --}}
            <flux:field>
                <flux:label>{{ __('End Date') }}</flux:label>
                <flux:input type="date" wire:model="end_date" />
                <flux:error name="end_date" />
            </flux:field>

            <div class="md:col-span-2 lg:col-span-3">
                <flux:field>
                    <flux:label>{{ __('Description') }}</flux:label>
                    <flux:textarea wire:model="description" :placeholder="__('Enter activity description...')"
                        rows="3" />
                    <flux:error name="description" />
                </flux:field>
            </div>

            {{-- Location Information Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg">{{ __('Location Details') }}</flux:heading>
            </div>

            {{-- Region --}}
            <flux:select wire:model.live="region" :label="__('Region')">
                <option value="">{{ __('Select Region') }}</option>
                @foreach ($regions as $r)
                    <option value="{{ $r->id }}">{{ $r->region_name }}</option>
                @endforeach
            </flux:select>
            <flux:error name="region" />

            {{-- City --}}
            <flux:select wire:model.live="city" :label="__('City')" :disabled="!$region">
                <option value="">{{ __('Select City') }}</option>
                @foreach ($cities as $c)
                    <option value="{{ $c->id }}">{{ $c->city_name }}</option>
                @endforeach
            </flux:select>
            <flux:error name="city" />

            {{-- Neighbourhood --}}
            <flux:select wire:model.live="neighbourhood" :label="__('Neighbourhood')" :disabled="!$city">
                <option value="">{{ __('Select Neighbourhood') }}</option>
                @foreach ($neighbourhoods as $n)
                    <option value="{{ $n->id }}">{{ $n->neighbourhood_name }}</option>
                @endforeach
            </flux:select>
            <flux:error name="neighbourhood" />

            {{-- Location --}}
            <flux:select wire:model.live="location" :label="__('Location')" :disabled="!$neighbourhood">
                <option value="">{{ __('Select Location') }}</option>
                @foreach ($locations as $l)
                    <option value="{{ $l->id }}">{{ $l->location_name }}</option>
                @endforeach
            </flux:select>
            <flux:error name="location" />

            {{-- Address Details --}}
            <div class="md:col-span-2 lg:col-span-2">
                <flux:field>
                    <flux:label>{{ __('Address Details') }}</flux:label>
                    <flux:input type="text" wire:model="address_details"
                        :placeholder="__('E.g. Building number, street name...')" />
                    <flux:error name="address_details" />
                </flux:field>
            </div>

              {{-- Status --}}
              <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">Status</flux:label>
                <flux:select wire:model="status">
                    <option value="">{{ __('Select Status') }}</option>
                    @foreach ($this->allStatuses->where('p_id_sub', config('appConstant.activity_status')) as $status)
                        <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="status" />
            </flux:field>

            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg" >{{ __('Activity Details') }}</flux:heading>
            </div>

            {{-- Parcels Section --}}
            <div class="md:col-span-2 lg:col-span-3">
                <div class="flex items-center justify-between mb-2">
                    <flux:heading size="lg" class="text-blue-600">{{ __('Parcels') }}</flux:heading>
                    <flux:button wire:click="addParcel" variant="ghost" icon="plus" size="sm">{{ __('Add Parcel') }}</flux:button>
                </div>
                <div class="space-y-4">
                    @foreach ($parcels as $index => $parcel)
                        <div wire:key="parcel-{{ $index }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 p-4 border rounded-lg border-zinc-200 dark:border-zinc-700 relative">
                            <flux:select wire:model="parcels.{{ $index }}.parcel_type" :label="__('Parcel Type')">
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($parcelTypes as $s)
                                    <option value="{{ $s->id }}">{{ $s->status_name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:input type="number" wire:model="parcels.{{ $index }}.distributed_parcels_count" :label="__('Count')" />
                            <flux:input type="number" step="0.01" wire:model="parcels.{{ $index }}.cost_for_each_parcel" :label="__('Cost Per Parcel')" />
                            <flux:select wire:model="parcels.{{ $index }}.status_id" :label="__('Status')">
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($activityStatuses as $s)
                                    <option value="{{ $s->id }}">{{ $s->status_name }}</option>
                                @endforeach
                            </flux:select>
                            <div class="flex items-end gap-2">
                                <flux:input type="text" wire:model="parcels.{{ $index }}.notes" :label="__('Notes')" />
                                <flux:button wire:click="removeParcel({{ $index }})" variant="ghost" icon="trash" class="text-red-500" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Beneficiaries Section --}}
            <div class="md:col-span-2 lg:col-span-3 mt-4">
                <div class="flex items-center justify-between mb-2">
                    <flux:heading size="lg" class="text-blue-600">{{ __('Beneficiaries') }}</flux:heading>
                    <flux:button wire:click="addBeneficiary" variant="ghost" icon="plus" size="sm">{{ __('Add Beneficiary') }}</flux:button>
                </div>
                <div class="space-y-4">
                    @foreach ($beneficiaries as $index => $beneficiary)
                        <div wire:key="beneficiary-{{ $index }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 p-4 border rounded-lg border-zinc-200 dark:border-zinc-700">
                            <flux:select wire:model="beneficiaries.{{ $index }}.beneficiary_type" :label="__('Type')">
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($this->allStatuses->where('p_id_sub', config('appConstant.beneficiaries_types')) as $s)
                                    <option value="{{ $s->id }}">{{ $s->status_name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:input type="number" wire:model="beneficiaries.{{ $index }}.beneficiaries_count" :label="__('Count')" />
                            <flux:input type="number" step="0.01" wire:model="beneficiaries.{{ $index }}.cost_for_each_beneficiary" :label="__('Cost Per Beneficiary')" />
                            <flux:select wire:model="beneficiaries.{{ $index }}.status_id" :label="__('Status')">
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($activityStatuses as $s)
                                    <option value="{{ $s->id }}">{{ $s->status_name }}</option>
                                @endforeach
                            </flux:select>
                            <div class="flex items-end gap-2">
                                <flux:input type="text" wire:model="beneficiaries.{{ $index }}.notes" :label="__('Notes')" />
                                <flux:button wire:click="removeBeneficiary({{ $index }})" variant="ghost" icon="trash" class="text-red-500" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Work Team Section --}}
            <div class="md:col-span-2 lg:col-span-3 mt-4">
                <div class="flex items-center justify-between mb-2">
                    <flux:heading size="lg" class="text-blue-600">{{ __('Work Team') }}</flux:heading>
                    <flux:button wire:click="addWorkTeam" variant="ghost" icon="plus" size="sm">{{ __('Add Member') }}</flux:button>
                </div>
                <div class="space-y-4">
                    @foreach ($work_teams as $index => $team)
                        <div wire:key="work-team-{{ $index }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 border rounded-lg border-zinc-200 dark:border-zinc-700">
                            <flux:select wire:model="work_teams.{{ $index }}.employee_id" :label="__('Employee')">
                                <option value="">{{ __('Select Employee') }}</option>
                                @foreach ($employees as $e)
                                    <option value="{{ $e->id }}">{{ $e->full_name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:select wire:model="work_teams.{{ $index }}.employee_mission_title" :label="__('Mission')">
                                <option value="">{{ __('Select Mission') }}</option>
                                @foreach ($this->allStatuses->where('p_id_sub', config('appConstant.aids_primary_missions')) as $s)
                                    <option value="{{ $s->id }}">{{ $s->status_name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:select wire:model="work_teams.{{ $index }}.status_id" :label="__('Status')">
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($activityStatuses as $s)
                                    <option value="{{ $s->id }}">{{ $s->status_name }}</option>
                                @endforeach
                            </flux:select>
                            <div class="flex items-end gap-2">
                                <flux:input type="text" wire:model="work_teams.{{ $index }}.notes" :label="__('Notes')" />
                                <flux:button wire:click="removeWorkTeam({{ $index }})" variant="ghost" icon="trash" class="text-red-500" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>


            {{-- Submit Button --}}
            <div class="md:col-span-2 lg:col-span-3 flex items-center justify-end gap-2 mt-6">
                <flux:button type="submit" variant="primary" icon="{{ $type === 'save' ? 'plus' : 'check' }}">
                    {{ $heading }}
                </flux:button>
            </div>
            @include('layouts._show_all_input_error')
        </form>
    </div>
</div>
