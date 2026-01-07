<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

    <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
        <x-app-logo />
    </a>



    <flux:navlist variant="outline">
        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
    </flux:navlist>

    <flux:sidebar.group expandable heading="Loan" class="grid">
        <flux:sidebar.item :href="route('loans.request')" :current="request()->routeIs('loans.request')" wire:navigate>Request</flux:sidebar.item>
        <flux:sidebar.item :href="route('loans.withdrawal')" :current="request()->routeIs('loans.withdrawal')" wire:navigate>Saving Withdrawal</flux:sidebar.item>
        <flux:sidebar.item :href="route('loans.pending')" :current="request()->routeIs('loans.pending')" wire:navigate>Pending</flux:sidebar.item>
    </flux:sidebar.group>

    <flux:sidebar.group expandable heading="Grain" class="grid">
        <flux:sidebar.item :href="route('grain.request')" :current="request()->routeIs('grain.request')" wire:navigate>Request</flux:sidebar.item>
        <flux:sidebar.item :href="route('grain.pending')" :current="request()->routeIs('grain.pending')" wire:navigate>Pending</flux:sidebar.item>
    </flux:sidebar.group>

    <flux:sidebar.group expandable heading="Guarantors" class="grid">
        <flux:sidebar.item :href="route('guarantor.request')" :current="request()->routeIs('guarantor.request')" wire:navigate>Request</flux:sidebar.item>
        <flux:sidebar.item :href="route('guarantor.history')" :current="request()->routeIs('guarantor.history')" wire:navigate>History</flux:sidebar.item>
    </flux:sidebar.group>


    <flux:spacer />

    <flux:navlist variant="outline">
        <flux:sidebar.item icon="folder-git-2" :href="route('saving.update')" :current="request()->routeIs('saving.update')" wire:navigate>Saving Update</flux:sidebar.item>

        <flux:sidebar.item icon="credit-card" :href="route('saving.history')" :current="request()->routeIs('saving.history')" wire:navigate>Saving History</flux:sidebar.item>

    </flux:navlist>

    <!-- Desktop User Menu -->
    <flux:dropdown class="hidden lg:block" position="bottom" align="start">
        <flux:profile
            :name="auth()->user()->name"
            :initials="auth()->user()->initials()"
            icon:trailing="chevrons-up-down"
        />

        <flux:menu class="w-[220px]">
            <flux:menu.radio.group>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                </div>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <flux:menu.radio.group>
                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>

<!-- Mobile User Menu -->
<flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <flux:spacer />

    <flux:dropdown position="top" align="end">
        <flux:profile
            :initials="auth()->user()->initials()"
            icon-trailing="chevron-down"
        />

        <flux:menu>
            <flux:menu.radio.group>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                </div>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <flux:menu.radio.group>
                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>


<flux:header>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item icon="home" />
        <flux:breadcrumbs.item>{{ $title }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>
</flux:header>
{{ $slot }}

@fluxScripts
</body>
</html>
