<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>
        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('App')" class="grid">
                <!-- Department Group -->
                @can('department.create')
                <flux:sidebar.group expandable :expanded="false" :heading="__('Department')" icon="flag"
                class="grid">
                <flux:sidebar.item icon="plus" :href="route('department.create')"
                    :current="request()->routeIs('department.create')" wire:navigate>
                    {{ __('Create Department') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="list-bullet" :href="route('department.index')"
                    :current="request()->routeIs('department.index')" wire:navigate>
                    {{ __('Department List') }}
                </flux:sidebar.item>

            </flux:sidebar.group>     
                @endcan
                

                <!-- Employees Group -->
                @can('employee.create')
                <flux:sidebar.group expandable :expanded="false" :heading="__('Employees')" icon="server-stack"
                    class="grid">
                    <flux:sidebar.item icon="plus" :href="route('employee.create')"
                        :current="request()->routeIs('employee.create')" wire:navigate>
                        {{ __('Create Employees') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="list-bullet" :href="route('employee.index')"
                        :current="request()->routeIs('employee.index')" wire:navigate>
                        {{ __('Employees List') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>    
                @endcan
                

                <!-- Activities Group -->
               
                    
                @can('activity.index')
                <flux:sidebar.group expandable :expanded="false" :heading="__('Activities')" icon="server-stack"
                    class="grid">
                    <flux:sidebar.item icon="list-bullet" :href="route('sector.show')"
                        :current="request()->routeIs('sector.show')" wire:navigate>
                        {{ __('Sectors & Activities') }}
                    </flux:sidebar.item>
                  
                    <flux:sidebar.item icon="list-bullet" :href="route('activity.index')"
                        :current="request()->routeIs('activity.index')" wire:navigate>
                        {{ __('Activities List') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('activity.create')
                    <flux:sidebar.item icon="plus" :href="route('activity.create')"
                        :current="request()->routeIs('activity.create')" wire:navigate>
                        {{ __('Create Activities') }}
                    </flux:sidebar.item>
                    @endcan
                   

                </flux:sidebar.group>


            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        <flux:sidebar.nav>



            <flux:sidebar.group :heading="__('Admin Setting')" class="grid">
                <!-- Status Group -->
                @can('status.create')
                    <flux:sidebar.group expandable :expanded="false" :heading="__('Status')" icon="flag"
                        class="grid">
                        <flux:sidebar.item icon="plus" :href="route('status.create')"
                            :current="request()->routeIs('status.create')" wire:navigate>
                            {{ __('Create Status') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="list-bullet" :href="route('status.index')"
                            :current="request()->routeIs('status.index')" wire:navigate>
                            {{ __('Status List') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="plus" :href="route('system.names.create')"
                            :current="request()->routeIs('system.names.create')" wire:navigate>
                            {{ __('Create System Name') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="list-bullet" :href="route('system.names.index')"
                            :current="request()->routeIs('system.names.index')" wire:navigate>
                            {{ __('System Names List') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endcan
                <!-- System Names Group -->
                @can('ability.create')
                    <flux:sidebar.group expandable :expanded="false" :heading="__('Permissions')" icon="server-stack"
                        class="grid">

                        <flux:sidebar.item icon="plus" :href="route('ability.create')"
                            :current="request()->routeIs('ability.create')" wire:navigate>
                            {{ __('Create Abilities') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="list-bullet" :href="route('ability.index')"
                            :current="request()->routeIs('system.names.index')" wire:navigate>
                            {{ __('Abilities List') }}
                        </flux:sidebar.item>
                    @endcan

                    @can('role.create')
                        <flux:sidebar.item icon="plus" :href="route('role.create')"
                            :current="request()->routeIs('role.create')" wire:navigate>
                            {{ __('Create Roles') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="list-bullet" :href="route('role.index')"
                            :current="request()->routeIs('role.index')" wire:navigate>
                            {{ __('Roles List') }}
                        </flux:sidebar.item>
                    @endcan


                </flux:sidebar.group>

                <!-- Users Group -->
                @can('user.create')
                    <flux:sidebar.group expandable :expanded="false" :heading="__('Users')" icon="users"
                        class="grid">
                        <flux:sidebar.item icon="plus" :href="route('user.create')"
                            :current="request()->routeIs('user.create')" wire:navigate>
                            {{ __('Create User') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="list-bullet" :href="route('user.index')"
                            :current="request()->routeIs('user.index')" wire:navigate>
                            {{ __('Users List') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endcan
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        <flux:sidebar.nav>
            <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit"
                target="_blank">
                {{ __('Repository') }}
            </flux:sidebar.item>
        
            <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire"
                target="_blank">
                {{ __('Documentation') }}
            </flux:sidebar.item>
        </flux:sidebar.nav>

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>
