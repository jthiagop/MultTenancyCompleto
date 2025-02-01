<div class="d-flex align-items-center">
    <a href="{{ route('file', ['path' => $file]) }}"
       target="_blank"
       class="text-gray-800 text-hover-primary">
    <i class="bi {{ $getIconClass() }} fs-2 text-primary me-4"></i>
        {{ basename($file) }}
    </a>
</div>
