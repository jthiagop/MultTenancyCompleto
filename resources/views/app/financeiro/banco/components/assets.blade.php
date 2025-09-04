<!-- CSS do Kendo (tema) -->
<link href="https://kendo.cdn.telerik.com/themes/8.0.1/default/default-main.css" rel="stylesheet" />

<!-- jQuery (obrigatório) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Kendo UI (JS principal) -->
<script src="https://kendo.cdn.telerik.com/2024.2.514/js/kendo.all.min.js"></script>

@include('app.components.modals.lancar-banco')
<!--end::Modal - Upgrade plan-->
<script>
    var lpsData = @json($lps);
</script>

<script src="/assets/js/custom_script.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<script src="/assets/js/custom/utilities/modals/financeiro/moduloAnexos.js"></script>
<script src="/assets/js/custom/utilities/modals/financeiro/new-banco.js"></script>

<script src="/assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
<script src="/assets/js/custom/apps/bancos/form-dropzone.js"></script>

<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/bancos/shipping.js"></script>
<script src="/assets/js/widgets.bundle.js"></script>
<script src="/assets/js/custom/apps/chat/chat.js"></script>
<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="/assets/js/custom/utilities/modals/users-search.js"></script>


<!--end::Custom Javascript-->
<!--end::Javascript-->

<!-- jQuery -->

<!-- Custom Script -->
<script src="{{ asset('js/custom_script.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteLinks = document.querySelectorAll('.delete-link');

        deleteLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const id = this.getAttribute('data-id');
                const form = document.getElementById(`delete-form-${id}`);
                Swal.fire({
                    title: 'Você tem certeza?',
                    text: 'Esta ação não pode ser desfeita!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, exclua!',
                    cancelButtonText: 'Não, cancele',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
