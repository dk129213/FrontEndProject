@props(['brand', 'size' => 32])
@php
    // Simple Icons (CC0) files in public/images/brands, drawn in the text colour.
    $files = [
        'Škoda' => 'skoda', 'Volkswagen' => 'volkswagen', 'Toyota' => 'toyota', 'Tesla' => 'tesla',
        'BMW' => 'bmw', 'Mercedes-Benz' => 'mercedes', 'Hyundai' => 'hyundai', 'Renault' => 'renault',
    ];
    $path = isset($files[$brand]) ? public_path('images/brands/'.$files[$brand].'.svg') : null;
    $svg = $path && file_exists($path) ? file_get_contents($path) : null;
    if ($svg) {
        $svg = preg_replace('/<title>.*?<\/title>/s', '', $svg);
        $svg = str_replace('<svg ', '<svg width="'.$size.'" height="'.$size.'" fill="currentColor" aria-hidden="true" focusable="false" ', $svg);
        $svg = str_replace(' role="img"', '', $svg);
    }
@endphp
@if ($svg)
    {!! $svg !!}
@else
    <span aria-hidden="true" style="font-weight: 800">{{ mb_substr($brand, 0, 1) }}</span>
@endif
