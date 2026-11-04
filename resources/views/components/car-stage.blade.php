@props(['car', 'eager' => false, 'size' => 'card'])
{{-- A car on the studio stage: the cut-out with a floor shadow, or the photo if there is no cut-out. --}}
<div {{ $attributes->class(['stage', 'stage--'.$size, 'stage--photo' => ! $car['cutout']]) }}>
    @if ($car['cutout'])
        <img class="stage__car" src="{{ $car['cutout'] }}" alt="{{ $car['title'] }}, {{ $car['colour'] }}" loading="{{ $eager ? 'eager' : 'lazy' }}" @if ($eager) fetchpriority="high" @endif width="1400" height="800">
    @else
        <img class="stage__photo" src="{{ $car['image'] }}" alt="{{ $car['title'] }}, {{ $car['colour'] }}" loading="{{ $eager ? 'eager' : 'lazy' }}" width="800" height="500">
    @endif
</div>
