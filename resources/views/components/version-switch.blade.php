@props(['cinematic' => false])
{{-- Lets the owner compare the two homepage versions. --}}
<nav class="version-switch" aria-label="Homepage version">
    <a href="{{ page('home') }}" @if (! $cinematic) aria-current="page" @endif>Standard</a>
    <a href="{{ page('cinematic') }}" @if ($cinematic) aria-current="page" @endif>Cinematic</a>
</nav>
