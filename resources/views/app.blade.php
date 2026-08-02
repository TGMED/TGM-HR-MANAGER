<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Chrome caches favicons in a per-origin store that survives a hard
             reload, and this hostname previously served a different app. Bump
             the version whenever an icon changes so existing visitors refetch. --}}
        <link rel="icon" href="/favicon.ico?v=2" sizes="any">
        <link rel="icon" href="/favicon-32.png?v=2" type="image/png" sizes="32x32">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=2">
        <meta name="theme-color" content="#0b1120">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
