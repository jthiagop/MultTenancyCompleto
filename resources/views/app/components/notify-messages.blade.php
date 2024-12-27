@if (session()->has('notify'))
    @foreach (session('notify') as $notification)
        <div class="alert alert-{{ $notification['type'] }} alert-dismissible fade show" role="alert">
            <strong>{{ $notification['title'] ?? '' }}</strong> {{ $notification['message'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach
@endif
