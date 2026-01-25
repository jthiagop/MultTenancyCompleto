"use strict";

var KTWebcamCapture = function() {
    var videoElement = null;
    var stream = null;
    var imageWrapper = null;
    var isCapturing = false;

    var init = function() {
        // Elementos do DOM
        var captureBtn = document.getElementById('kt_webcam_capture_btn');
        var videoEl = document.getElementById('kt_webcam_video_inline');
        var captureInlineBtn = document.getElementById('kt_webcam_capture_inline_btn');
        var avatarChangeLabel = document.getElementById('kt_avatar_change_label');
        var avatarRemoveBtn = document.getElementById('kt_avatar_remove_btn');

        if (!captureBtn || !videoEl) return;

        videoElement = videoEl;
        imageWrapper = document.querySelector('.image-input-wrapper');

        // Iniciar captura
        captureBtn.addEventListener('click', function() {
            startWebcam();
        });

        // Capturar foto
        captureInlineBtn.addEventListener('click', function() {
            capturePhoto();
        });

        // Botão de remover funciona tanto para cancelar captura quanto remover foto salva
        if (avatarRemoveBtn) {
            avatarRemoveBtn.addEventListener('click', function(e) {
                if (isCapturing) {
                    // Se estiver capturando, cancela a captura
                    e.preventDefault();
                    stopWebcam();
                    return false;
                }
                // Se não estiver capturando, o comportamento padrão do image-input remove a foto
                // Não precisa fazer nada, o sistema do Metronic já cuida disso
            });
        }

        // Desabilitar botão de alterar durante captura
        if (avatarChangeLabel) {
            avatarChangeLabel.addEventListener('click', function(e) {
                if (isCapturing) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    };

    var startWebcam = function() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Seu navegador não suporta captura de webcam ou a câmera não está disponível.',
            });
            return;
        }

        isCapturing = true;

        // Esconder background image
        if (imageWrapper) {
            imageWrapper.style.backgroundImage = 'none';
        }

        // Mostrar vídeo
        videoElement.classList.remove('d-none');

        // Mostrar botão de captura
        document.getElementById('kt_webcam_capture_inline_btn').classList.remove('d-none');

        // O botão de remover já está visível e funcionará para cancelar a captura

        // Esconder botão de captura externo
        document.getElementById('kt_webcam_capture_btn').classList.add('d-none');

        // Solicitar acesso à webcam
        navigator.mediaDevices.getUserMedia({
                video: {
                    width: {
                        ideal: 640
                    },
                    height: {
                        ideal: 640
                    },
                    facingMode: 'user' // Câmera frontal
                },
                audio: false
            })
            .then(function(mediaStream) {
                stream = mediaStream;
                videoElement.srcObject = stream;
                videoElement.play();

                // Ajustar zoom quando o vídeo estiver pronto
                videoElement.addEventListener('loadedmetadata', function() {
                    adjustVideoZoom();
                });
            })
            .catch(function(error) {
                console.error('Erro ao acessar webcam:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Não foi possível acessar a câmera. Verifique as permissões do navegador.',
                });
                stopWebcam();
            });
    };

    var capturePhoto = function() {
        if (!videoElement || !stream) return;

        // Criar canvas para capturar frame
        var canvas = document.createElement('canvas');
        var size = 400; // Tamanho final da imagem
        canvas.width = size;
        canvas.height = size;
        var ctx = canvas.getContext('2d');

        // Calcular dimensões para manter proporção e centralizar
        var videoWidth = videoElement.videoWidth;
        var videoHeight = videoElement.videoHeight;
        var minDimension = Math.min(videoWidth, videoHeight);
        var sourceX = (videoWidth - minDimension) / 2;
        var sourceY = (videoHeight - minDimension) / 2;

        // Desenhar imagem centralizada e quadrada
        ctx.drawImage(
            videoElement,
            sourceX, sourceY, minDimension, minDimension, // Source (quadrado centralizado)
            0, 0, size, size // Destination
        );

        // Aplicar máscara circular
        ctx.globalCompositeOperation = 'destination-in';
        ctx.beginPath();
        ctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
        ctx.fill();

        // Converter canvas para blob
        canvas.toBlob(function(blob) {
            if (!blob) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Não foi possível processar a imagem.',
                });
                return;
            }

            // Criar File object
            var file = new File([blob], 'avatar_' + Date.now() + '.jpg', {
                type: 'image/jpeg',
                lastModified: Date.now()
            });

            // Criar DataTransfer para anexar ao input
            var dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);

            // Anexar ao input file
            var avatarInput = document.getElementById('avatar_input');
            avatarInput.files = dataTransfer.files;

            // Atualizar preview usando FileReader (como o image-input do Metronic faz)
            var reader = new FileReader();
            reader.onload = function(e) {
                updateAvatarPreview(e.target.result);

                // Marcar como alterado
                var imageInputElement = document.querySelector('[data-kt-image-input="true"]');
                if (imageInputElement) {
                    imageInputElement.classList.add('image-input-changed');
                    imageInputElement.classList.remove('image-input-empty');
                }

                // Parar webcam
                stopWebcam();

                // Mostrar mensagem de sucesso
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: 'Foto capturada com sucesso!',
                    timer: 1500,
                    showConfirmButton: false
                });
            };
            reader.readAsDataURL(file);

            // Disparar evento change para garantir que outros handlers sejam executados
            var changeEvent = new Event('change', {
                bubbles: true
            });
            avatarInput.dispatchEvent(changeEvent);
        }, 'image/jpeg', 0.9);
    };

    var updateAvatarPreview = function(imageDataUrl) {
        // Atualizar o wrapper do image-input
        var imageInputElement = document.querySelector('[data-kt-image-input="true"]');
        if (imageInputElement) {
            var wrapper = imageInputElement.querySelector('.image-input-wrapper');
            if (wrapper) {
                wrapper.style.backgroundImage = 'url(' + imageDataUrl + ')';
            }

            // Garantir que os botões permaneçam visíveis
            var changeLabel = document.getElementById('kt_avatar_change_label');
            var removeBtn = document.getElementById('kt_avatar_remove_btn');

            if (changeLabel) {
                changeLabel.style.display = '';
                changeLabel.style.visibility = 'visible';
                changeLabel.style.opacity = '1';
                changeLabel.style.zIndex = '10';
            }

            if (removeBtn) {
                removeBtn.style.display = '';
                removeBtn.style.visibility = 'visible';
                removeBtn.style.opacity = '1';
                removeBtn.style.zIndex = '10';
            }
        }
    };

    var stopStream = function() {
        if (stream) {
            stream.getTracks().forEach(function(track) {
                track.stop();
            });
            stream = null;
        }
        if (videoElement) {
            videoElement.srcObject = null;
        }
    };

    var adjustVideoZoom = function() {
        if (!videoElement || !imageWrapper) return;

        var videoWidth = videoElement.videoWidth;
        var videoHeight = videoElement.videoHeight;
        var wrapperSize = 150; // 150px do wrapper

        if (videoWidth && videoHeight) {
            var videoAspect = videoWidth / videoHeight;
            var wrapperAspect = 1; // Círculo = 1:1

            // Calcular scale para mostrar mais área (reduzir zoom)
            var scale;
            if (videoAspect > wrapperAspect) {
                // Vídeo é mais largo - ajustar pela altura
                scale = (wrapperSize * 1.1) / videoHeight; // 1.1 = 10% de redução no zoom
            } else {
                // Vídeo é mais alto - ajustar pela largura
                scale = (wrapperSize * 1.1) / videoWidth;
            }

            var scaledWidth = videoWidth * scale;
            var scaledHeight = videoHeight * scale;

            videoElement.style.width = scaledWidth + 'px';
            videoElement.style.height = scaledHeight + 'px';
            videoElement.style.objectFit = 'cover';
        }
    };

    var stopWebcam = function() {
        isCapturing = false;

        // Parar stream
        stopStream();

        // Esconder vídeo
        videoElement.classList.add('d-none');

        // Resetar estilos do vídeo
        videoElement.style.width = '';
        videoElement.style.height = '';

        // Esconder botão de captura
        document.getElementById('kt_webcam_capture_inline_btn').classList.add('d-none');

        // O botão de remover permanece visível para remover foto salva

        // Mostrar botão de captura externo
        document.getElementById('kt_webcam_capture_btn').classList.remove('d-none');

        // Restaurar background image se não houver foto
        if (imageWrapper && !imageWrapper.style.backgroundImage.includes('data:image')) {
            imageWrapper.style.backgroundImage = "url('assets/media/avatars/blank.png')";
        }
    };

    return {
        init: init
    };
}();

// Inicializar quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        KTWebcamCapture.init();
    });
} else {
    KTWebcamCapture.init();
}

