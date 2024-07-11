<div class="clear-both"></div>

@if(!empty(session('success')))
<div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800" role="alert">
    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
      <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
    </svg>
    <span class="sr-only">Info</span>
    <div>
      <span class="font-medium">Success alert!</span> Change a few things up and try submitting again.
    </div>
  </div>
@endif

@if(!empty(session('error')))
<div class="alert alert-danger" role="alert">
    {{ session('error') }}
</div>
@endif



@if(!empty(session('warning')))
<div class="alert alert-warning " role="alert">
    {{ session('warning') }}
</div>
@endif

@if(!empty(session('info')))
<div class="alert alert-info " role="alert">
    {{ session('info') }} <!-- Corrected from 'into' to 'info' -->
</div>
@endif

@if(!empty(session('secondary')))
<div class="alert alert-secondary " role="alert">
    {{ session('secondary') }}
</div>
@endif

@if(!empty(session('primary')))
<div class="alert alert-primary " role="alert">
    {{ session('primary') }}
</div>
@endif

@if(!empty(session('light')))
<div class="alert alert-light " role="alert">
    {{ session('light') }}
</div>
@endif
