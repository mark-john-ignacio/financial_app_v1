<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Page Title' }}</title>

        @vite(\Nwidart\Modules\Module::getAssets())
    </head>
    <body style="height:750px">
    <div class="container-fluid mt-1">
        {{ $slot }}
    </div>
    {{-- Include the stacked scripts --}}
    @stack('scripts')
    </body>
</html>
