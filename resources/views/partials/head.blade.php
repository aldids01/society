<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/images/favicon.ico" sizes="any">
<link rel="icon" href="/images/favicon.ico" type="image/svg+xml">
<link rel="apple-touch-icon" href="/images/favicon.ico">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

@filamentStyles
@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
@livewire('notifications')
@filamentScripts
