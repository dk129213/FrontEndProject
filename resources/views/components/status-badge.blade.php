@props(['status'])
@php
    // One vocabulary on both sides: the word always travels with the colour.
    $tones = [
        'available' => 'success', 'reserved' => 'warning', 'sold' => '', 'draft' => 'sea',
        'paid' => 'success', 'pending' => 'warning', 'failed' => 'danger',
        'completed' => 'success', 'requested' => 'sea', 'cancelled' => '',
        'new' => 'signal', 'active' => 'success', 'hidden' => '',
    ];
    $tone = $tones[$status] ?? '';
@endphp
<span {{ $attributes->class(['badge', "badge--{$tone}" => $tone]) }}>{{ $slot->isEmpty() ? ucfirst($status) : $slot }}</span>
