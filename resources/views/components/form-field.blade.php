@props([
    'label' => '',
    'name' => '',
    'required' => false,
    'gridClass' => '',
])

<div class="{{ $gridClass ?: 'mb-4' }}">
    @if($label)
        <label for="{{ $name }}" class="form-label {{ $required ? 'form-label-required' : '' }}">
            {{ $label }}
        </label>
    @endif
    {{ $slot }}
    @error($name)
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>
