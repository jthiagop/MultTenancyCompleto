@props(['class' => ''])

<div class="d-flex flex-row flex-wrap py-5 gap-5 {{ $class }}">
    {{ $slot }}
</div>

