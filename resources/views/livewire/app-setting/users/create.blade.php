<div class="flex flex-col gap-6">

    {{-- <nav class="flex px-5 py-3 text-gray-700 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="#" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">App Settings</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ route('user.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Roles</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Grant User Role</span>
                </div>
            </li>
        </ol>
    </nav> --}}

    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{$heading ?? 'Create New User'}}</flux:heading>
            <flux:subheading>{{$subheading ?? 'Enter the details for your new User below.'}}</flux:subheading>
        </div>
        
        <flux:button 
            href="{{route('user.index')}}" 
            wire:navigate 
            variant="primary"
            icon="list-bullet"
        >
            {{ __('User List') }}
        </flux:button>
    </div>
    <x-auth-session-status class="text-center" :status="session('message')" />
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
    <form wire:submit="store" class="flex flex-col gap-6">
        <!-- Name -->


        <flux:field>
            <flux:label badge="Required" badgeColor="text-red-600">Name</flux:label>
            <flux:input type="text" wire:model="name" autofocus autocomplete="name" class="md:col-span-2"
                :placeholder="__('Full name')" />
            <flux:error name="name" />
        </flux:field>

        <!-- Email Address -->

        <flux:field>
            <flux:label badge="Required" badgeColor="text-red-600">Email</flux:label>
            <flux:input type="email" wire:model="email" autocomplete="email" class="md:col-span-2"
                placeholder="email@example.com" />
            <flux:error name="email" />
        </flux:field>

        <!-- Password -->
        {{-- <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
            viewable
        /> --}}


        <flux:field>
            <flux:label badge="Required" badgeColor="text-red-600">Password</flux:label>
            <flux:input type="password" wire:model="password" autocomplete="new-password" class="md:col-span-2"
                placeholder="{{ __('Password') }}" viewable />
            <flux:error name="password" />
        </flux:field>

        <!-- Confirm Password -->
        <flux:field>
            <flux:label badge="Required" badgeColor="text-red-600">Password Confirmation</flux:label>
            <flux:input type="password" wire:model.defer="password_confirmation" autocomplete="new-password"
                class="md:col-span-2" placeholder="{{ __('Password confirmation') }}" viewable />
            <flux:error name="password_confirmation" />
        </flux:field>

        <div class="flex items-center justify-end gap-2">
            <flux:button type="submit" variant="primary">
                {{ __('Create User') }}
            </flux:button>
        </div>
    </form>
</div>
</div>
