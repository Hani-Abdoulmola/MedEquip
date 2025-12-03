@props(['alt' => 'MedEquip Logo'])

<img src="{{ asset('assets/img/Caduceus Icon.png') }}" alt="{{ $alt }}"
    {{ $attributes->merge(['loading' => 'lazy', 'decoding' => 'async']) }}>
