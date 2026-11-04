@props(['current', 'car'])
@php
    $steps = [
        'car' => ['Choose a car', $car['url']],
        'extras' => ['Choose extras', page('checkout.extras', $car['slug'])],
        'pay' => ['Sign in and pay', null],
    ];
    $order = array_keys($steps);
    $currentIndex = array_search($current, $order);
@endphp
<ol class="stepper" aria-label="Checkout progress">
    @foreach ($steps as $key => [$label, $href])
        @php($index = array_search($key, $order))
        <li @class(['is-done' => $index < $currentIndex]) @if ($index === $currentIndex) aria-current="step" @endif>
            @if ($index < $currentIndex && $href)
                <a href="{{ $href }}">{{ $label }}</a>
            @else
                {{ $label }}
            @endif
        </li>
    @endforeach
</ol>
