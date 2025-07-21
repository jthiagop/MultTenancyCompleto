
"use strict";

// Class definition
var KTModalEmitirPtam = function () {
    var submitButton;
    var cancelButton;
    var validator;
    var form;
    var modal;
    var modalEl;

    // Init form inputs
    var initForm = function() {
        // Initialize Select2 for PTAM type in the main form
        $('#ptam_type').select2({
            minimumResultsForSearch: Infinity,
            dropdownCssClass: 'w-250px'
        });

        // Handle PTAM type change for dynamic calculation
        document.getElementById('ptam_type').addEventListener('change', function () {
            const valorImovel = parseFloat(document.getElementById('valor_imovel').value) || 0;
            const ptamType = this.value;
            const dominioDiretoPercentage = document.getElementById('dominio_direto_percentage');
            let valorCalculado = 0;

            if (ptamType === 'foro') {
                valorCalculado = valorImovel * 0.0023;
                dominioDiretoPercentage.style.display = 'none';
            } else if (ptamType === 'laudemio') {
                valorCalculado = valorImovel * 0.025;
                dominioDiretoPercentage.style.display = 'none';
            } else if (ptamType === 'dominio_direto') {
                dominioDiretoPercentage.style.display = 'block';
                const customPercentage = parseFloat(document.getElementById('custom_percentage').value) || 8;
                valorCalculado = valorImovel * (customPercentage / 100);
            } else {
                dominioDiretoPercentage.style.display = 'none';
            }

            const formattedValor = valorCalculado.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            document.getElementById('valor_calculado').textContent = formattedValor;
            document.getElementById('valor_calculado_input').value = valorCalculado.toFixed(2);
        });

        // Handle custom percentage input for Domínio Direto
        document.getElementById('custom_percentage').addEventListener('input', function () {
            const ptamType = document.getElementById('ptam_type').value;
            if (ptamType === 'dominio_direto') {
                const valorImovel = parseFloat(document.getElementById('valor_imovel').value) || 0;
                const customPercentage = parseFloat(this.value) || 8;
                const valorCalculado = valorImovel * (customPercentage / 100);
                const formattedValor = valorCalculado.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                document.getElementById('valor_calculado').textContent = formattedValor;
                document.getElementById('valor_calculado_input').value = valorCalculado.toFixed(2);
            }
        });

        // Populate modal fields when opened
        $('#kt_modal_emitir_ptam').on('show.bs.modal', function () {
            const ptamType = document.getElementById('ptam_type').value;
            const valorCalculado = document.getElementById('valor_calculado_input').value;
            const customPercentage = document.getElementById('custom_percentage').value;

            document.getElementById('ptam_type_display').value = ptamType ? ptamType.charAt(0).toUpperCase() + ptamType.slice(1) : '';
            document.getElementById('ptam_type_confirm').value = ptamType;
            document.getElementById('valor_calculado_display').value = valorCalculado ? parseFloat(valorCalculado).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : 'R$ 0,00';
            document.getElementById('valor_calculado_confirm').value = valorCalculado;

            if (ptamType === 'dominio_direto') {
                document.getElementById('dominio_direto_percentage_confirm').style.display = 'block';
                document.getElementById('custom_percentage_display').value = customPercentage ? customPercentage + '%' : '8%';
                document.getElementById('custom_percentage_confirm').value = customPercentage;
            } else {
                document.getElementById('dominio_direto_percentage_confirm').style.display = 'none';
            }
        });
    }

    // Handle form validation and submission
    var handleForm = function() {
        // Init form validation rules
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    ptam_type: {
                        validators: {
                            notEmpty: {
                                message: 'O tipo de PTAM é obrigatório'
                            }
                        }
                    },
                    observacoes: {
                        validators: {
                            notEmpty: {
                                message: 'As observações são obrigatórias'
                            }
                        }
                    },
                    custom_percentage: {
                        validators: {
                            callback: {
                                message: 'O percentual deve estar entre 8% e 12%',
                                callback: function(input) {
                                    if (form.querySelector('[name="ptam_type"]').value === 'dominio_direto') {
                                        const value = parseFloat(input.value);
                                        return value >= 8 && value <= 12;
                                    }
                                    return true;
                                }
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );

        // Action buttons
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            // Validate form before submit
            if (validator) {
                validator.validate().then(function (status) {
                    if (status === 'Valid') {
                        submitButton.setAttribute('data-kt-indicator', 'on');
                        submitButton.disabled = true;

                        setTimeout(function() {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            Swal.fire({
                                text: "PTAM gerado com sucesso!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, entendi!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    modal.hide();
                                    form.submit();
                                }
                            });
                        }, 2000);
                    } else {
                        Swal.fire({
                            text: "Desculpe, foram detectados alguns erros. Por favor, tente novamente.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, entendi!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                });
            }
        });

        cancelButton.addEventListener('click', function (e) {
            e.preventDefault();

            Swal.fire({
                text: "Tem certeza que deseja cancelar?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Sim, cancelar!",
                cancelButtonText: "Não, voltar",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.value) {
                    form.reset();
                    modal.hide();
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "O formulário não foi cancelado!",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, entendi!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });
        });
    }

    return {
        init: function () {
            modalEl = document.querySelector('#kt_modal_emitir_ptam');
            if (!modalEl) {
                return;
            }

            modal = new bootstrap.Modal(modalEl);
            form = document.querySelector('#kt_modal_emitir_ptam_confirm_form');
            submitButton = document.getElementById('kt_modal_emitir_ptam_submit');
            cancelButton = document.getElementById('kt_modal_emitir_ptam_cancel');

            initForm();
            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalEmitirPtam.init();
});
