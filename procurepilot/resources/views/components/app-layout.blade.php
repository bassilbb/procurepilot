@props(['title' => null, 'header' => null])

@php
    if ($title) {
        \Illuminate\Support\Facades\View::share('pageTitle', $title);
    }
@endphp

@if ($header)
    <x-layouts.app :header="$header">
        {{ $slot }}
    </x-layouts.app>
@else
    <x-layouts.app>
        {{ $slot }}
    </x-layouts.app>
@endif
