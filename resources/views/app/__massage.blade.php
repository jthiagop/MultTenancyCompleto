<div class="clear-both"></div>

@if(!empty(session('success')))
<script>
Command: toastr["success"]("Are you the six fingered man?", "Sucesso")

toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": false,
  "progressBar": true,
  "positionClass": "toast-bottom-right",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}
</script>
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
