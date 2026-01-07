<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">




<flux:header>
    <a href="/" style="width: 20%; height: 50%" wire:navigate>
        <x-app-logo />
    </a>
</flux:header>
{{ $slot }}

@fluxScripts
</body>
</html>
