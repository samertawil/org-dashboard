<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

@php
    // $routesToHibernate = ['*.index', 'student.group.schedule*', 'activity.index', 'sector.show*'];
    $routesToHibernate = [''];

    $shouldHibernate = collect($routesToHibernate)->contains(fn($pattern) => request()->routeIs($pattern));
@endphp

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable="false" :collapsible="true"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:sidebar.collapse class="hidden lg:flex" id="flux-sidebar-toggle-desktop" />
        </flux:sidebar.header>





        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="calendar" :href="route('calendar.index')"
                    :current="request()->routeIs('calendar.index')" wire:navigate>
                    {{ __('Calendar') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="photo" :href="route('gallery.index')"
                    :current="request()->routeIs('gallery.index')" wire:navigate>
                    {{ __('Gallery') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

        </flux:sidebar.nav>


        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('App')" class="grid">


                @canany(['subject.index', 'subject.create', 'student.group.index', 'student.group.create',
                    'student.index', 'student.create', 'reports.groups.attendance'])
                    <flux:sidebar.group expandable :expanded="false" :heading="__('Education')" icon="book-open-text"
                        class="grid">

                        @can('subject.index')
                            <flux:sidebar.item icon="list-bullet" :href="route('subject.index')"
                                :current="request()->routeIs('subject.index')" wire:navigate>
                                {{ __('Curricula List') }}
                            </flux:sidebar.item>
                        @endcan

                        @can('student.group.index')
                            <flux:sidebar.item icon="rectangle-group" :href="route('student.group.index')"
                                :current="request()->routeIs('student.group.index')" wire:navigate>
                                {{ __('Students Groups List') }}
                            </flux:sidebar.item>
                        @endcan

                        @can('student.index')
                            <flux:sidebar.item icon="academic-cap" :href="route('student.index')"
                                :current="request()->routeIs('student.index')" wire:navigate>
                                {{ __('Students List') }}
                            </flux:sidebar.item>
                        @endcan
                        @can('reports.groups.attendance')
                            <flux:sidebar.item icon="document-text" :href="route('reports.groups.attendance')"
                                :current="request()->routeIs('reports.groups.attendance')" wire:navigate>
                                {{ __('Groups Attendance') }}
                            </flux:sidebar.item>
                        @endcan

                    </flux:sidebar.group>
                @endcanany


                <!-- Activities Group -->


                @canany(['activity.index', 'activity.create', 'sector.show'])
                    <flux:sidebar.group expandable :expanded="false" :heading="__('Activities')"
                        icon="clipboard-document-list" class="grid">

                        @can('activity.index')
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

                        @can('sector.show')
                            <flux:sidebar.item icon="list-bullet" :href="route('sector.show')"
                                :current="request()->routeIs('sector.show')" wire:navigate>
                                {{ __('Sectors & Activities') }}
                            </flux:sidebar.item>
                        @endcan
                    </flux:sidebar.group>
                @endcanany

                <!-- Purchase Group -->
                @canany(['purchase_request.index', 'purchase_request.create'])
                    <flux:sidebar.group expandable :expanded="false" :heading="__('Purchase')" icon="shopping-cart"
                        class="grid">
                        @can('purchase_request.index')
                            <flux:sidebar.item icon="list-bullet" :href="route('purchase_request.index')"
                                :current="request()->routeIs('purchase_request.index')" wire:navigate>
                                {{ __('Purchase List') }}
                            </flux:sidebar.item>
                        @endcan

                    </flux:sidebar.group>
                @endcanany
                <!-- Statistics  Group -->

                @canany(['reports.all'])
                    <flux:sidebar.group expandable :expanded="false" :heading="__('Statistics')" icon="chart-pie"
                        class="grid">
                        <flux:sidebar.item icon="presentation-chart-line" :href="route('reports.activity.overview')"
                            :current="request()->routeIs('reports.activity.overview')" wire:navigate>
                            {{ __('Activity Reports') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="currency-dollar" :href="route('reports.financial.summary')"
                            :current="request()->routeIs('reports.financial.summary')" wire:navigate>
                            {{ __('Financial Reports') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="user-group" :href="route('reports.beneficiary.impact')"
                            :current="request()->routeIs('reports.beneficiary.impact')" wire:navigate>
                            {{ __('Beneficiaries Reports') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="academic-cap" :href="route('reports.educational.progress')"
                            :current="request()->routeIs('reports.educational.progress')" wire:navigate>
                            {{ __('Educational Reports') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="star" :href="route('reports.feedback.analysis')"
                            :current="request()->routeIs('reports.feedback.analysis')" wire:navigate>
                            {{ __('Feedback Reports') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endcanany
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        <flux:sidebar.nav>

     @canany(['department.index', 'department.create', 'employee.index', 'employee.create', 'partner.index',
                    'partner.create', 'status.index', 'status.create', 'system.names.index',
                    'system.names.create', 'ability.create', 'ability.index', 'role.create', 'role.index',
                    'user.create', 'user.index', 'currency.index', 'currency.create'])
            <flux:sidebar.group :heading="__('Admin Setting')" class="grid">
        @endcanany
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
                    <flux:sidebar.group expandable :expanded="false" :heading="__('Employees')" icon="user-group"
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

                @canany(['currency.index', 'currency.create'])
                    <flux:sidebar.group expandable :expanded="false" :heading="__('Currency')" icon="currency-dollar"
                        class="grid">
                        @can('currency.create')
                            <flux:sidebar.item icon="plus" :href="route('currency.create')"
                                :current="request()->routeIs('currency.create')" wire:navigate>
                                {{ __('Create Currency') }}
                            </flux:sidebar.item>
                        @endcan

                        @can('currency.index')
                            <flux:sidebar.item icon="list-bullet" :href="route('currency.index')"
                                :current="request()->routeIs('currency.index')" wire:navigate>
                                {{ __('Currency List') }}
                            </flux:sidebar.item>
                        @endcan
                    </flux:sidebar.group>
                @endcanany




                <!-- Partners Group -->
                @canany(['partner.index', 'partner.create'])
                    <flux:sidebar.group expandable :expanded="false" :heading="__('Partners')" icon="briefcase"
                        class="grid">
                        @can('partner.create')
                            <flux:sidebar.item icon="plus" :href="route('partner.create')"
                                :current="request()->routeIs('partner.create')" wire:navigate>
                                {{ __('Create Partners') }}
                            </flux:sidebar.item>
                        @endcan
                        @can('partner.index')
                            <flux:sidebar.item icon="list-bullet" :href="route('partner.index')"
                                :current="request()->routeIs('partner.index')" wire:navigate>
                                {{ __('Partners List') }}
                            </flux:sidebar.item>
                        @endcan
                    </flux:sidebar.group>
                @endcanany
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
                    <flux:sidebar.group expandable :expanded="false" :heading="__('Permissions')" icon="key"
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

    <script>
        document.addEventListener('livewire:navigated', () => {
            if (window.innerWidth < 1024) return;

            const currentRoute = document.querySelector('meta[name="current-route"]')?.content;
            const patterns = @json($routesToHibernate);

            const matchPattern = (route, pattern) => {
                if (!route) return false;
                const regex = new RegExp('^' + pattern.replace(/\*/g, '.*') + '$');
                return regex.test(route);
            };

            const shouldHibernate = patterns.some(pattern => matchPattern(currentRoute, pattern));

            if (shouldHibernate) {
                setTimeout(() => {
                    const toggleWrapper = document.getElementById('flux-sidebar-toggle-desktop');
                    const sidebar = toggleWrapper?.closest('[data-flux-sidebar]');

                    if (sidebar && !sidebar.hasAttribute('data-flux-sidebar-collapsed-desktop')) {
                        const innerBtn = toggleWrapper?.querySelector('button');
                        if (innerBtn) {
                            innerBtn.click();
                        } else {
                            window.dispatchEvent(new CustomEvent('flux-sidebar-toggle'));
                        }
                    }
                }, 100);
            }
        });
    </script>

    @fluxScripts
</body>

</html>
