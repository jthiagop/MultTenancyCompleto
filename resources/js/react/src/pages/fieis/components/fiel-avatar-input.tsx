import { useCallback, useEffect, useRef, useState } from 'react';
import { Camera, Pencil, Upload, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { notify } from '@/lib/notify';
import { cn } from '@/lib/utils';

const ACCEPT = 'image/jpeg,image/png,image/jpg,.jpg,.jpeg,.png';
const MAX_BYTES = 2048 * 1024;
const BLANK_AVATAR = '/tenancy/assets/media/avatars/blank.png';

export interface FielAvatarInputProps {
  value: File | null;
  onChange: (file: File | null) => void;
  disabled?: boolean;
  className?: string;
}

/**
 * Avatar circular (~150px) — upload de arquivo ou captura via webcam,
 * alinhado ao modal Blade `cadastro_fiel`.
 */
export function FielAvatarInput({ value, onChange, disabled, className }: FielAvatarInputProps) {
  const fileInputRef = useRef<HTMLInputElement>(null);
  const videoRef = useRef<HTMLVideoElement>(null);
  const streamRef = useRef<MediaStream | null>(null);
  const previewUrlRef = useRef<string | null>(null);

  const [previewUrl, setPreviewUrl] = useState<string | null>(null);
  const [webcamActive, setWebcamActive] = useState(false);

  const revokePreview = useCallback(() => {
    if (previewUrlRef.current) {
      URL.revokeObjectURL(previewUrlRef.current);
      previewUrlRef.current = null;
    }
  }, []);

  const setPreviewFromFile = useCallback(
    (file: File | null) => {
      revokePreview();
      if (!file) {
        setPreviewUrl(null);
        return;
      }
      const url = URL.createObjectURL(file);
      previewUrlRef.current = url;
      setPreviewUrl(url);
    },
    [revokePreview],
  );

  useEffect(() => {
    setPreviewFromFile(value);
    return () => {
      revokePreview();
    };
  }, [value, setPreviewFromFile, revokePreview]);

  const stopWebcam = useCallback(() => {
    streamRef.current?.getTracks().forEach((t) => t.stop());
    streamRef.current = null;
    setWebcamActive(false);
    if (videoRef.current) {
      videoRef.current.srcObject = null;
    }
  }, []);

  useEffect(() => () => stopWebcam(), [stopWebcam]);

  useEffect(() => {
    if (!webcamActive || !videoRef.current || !streamRef.current) return;
    videoRef.current.srcObject = streamRef.current;
    void videoRef.current.play().catch(() => {});
  }, [webcamActive]);

  async function startWebcam() {
    if (disabled || webcamActive) return;
    try {
      const stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'user' },
        audio: false,
      });
      streamRef.current = stream;
      setWebcamActive(true);
    } catch {
      notify.error(
        'Câmera',
        'Não foi possível acessar a webcam. Verifique permissões do navegador ou use o envio de arquivo.',
      );
    }
  }

  function captureFromWebcam() {
    const video = videoRef.current;
    if (!video || video.videoWidth === 0) {
      notify.error('Câmera', 'Aguarde a imagem da câmera estabilizar e tente novamente.');
      return;
    }
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;
    ctx.drawImage(video, 0, 0);
    canvas.toBlob(
      (blob) => {
        if (!blob) {
          notify.error('Câmera', 'Não foi possível gerar a imagem.');
          return;
        }
        const file = new File([blob], 'webcam-capture.jpg', { type: 'image/jpeg' });
        if (file.size > MAX_BYTES) {
          notify.error('Arquivo', 'A foto capturada excede 2 MB. Tente outra captura ou envie um arquivo menor.');
          return;
        }
        stopWebcam();
        onChange(file);
      },
      'image/jpeg',
      0.92,
    );
  }

  function handleFilePick(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    e.target.value = '';
    if (!file) return;
    if (!/^image\/(jpeg|jpg|png)$/i.test(file.type)) {
      notify.error('Arquivo', 'Use apenas imagens PNG ou JPG.');
      return;
    }
    if (file.size > MAX_BYTES) {
      notify.error('Arquivo', 'A imagem deve ter no máximo 2 MB.');
      return;
    }
    stopWebcam();
    onChange(file);
  }

  function handleRemove() {
    stopWebcam();
    onChange(null);
  }

  const showVideo = webcamActive;
  const displaySrc = showVideo ? undefined : previewUrl || BLANK_AVATAR;

  return (
    <div className={cn('flex flex-col items-center gap-2', className)}>
      <input
        ref={fileInputRef}
        type="file"
        accept={ACCEPT}
        className="hidden"
        disabled={disabled}
        onChange={handleFilePick}
      />

      <div className="relative mb-7 size-[150px] shrink-0">
        <div
          className={cn(
            'size-[150px] rounded-full overflow-hidden border-2 border-border bg-muted relative',
            !disabled && !webcamActive && 'cursor-pointer',
          )}
          onClick={() => !disabled && !webcamActive && fileInputRef.current?.click()}
          role={!disabled && !webcamActive ? 'button' : undefined}
          tabIndex={!disabled && !webcamActive ? 0 : undefined}
          onKeyDown={(e) => {
            if (!disabled && !webcamActive && (e.key === 'Enter' || e.key === ' ')) {
              e.preventDefault();
              fileInputRef.current?.click();
            }
          }}
        >
          {showVideo ? (
            <video
              ref={videoRef}
              autoPlay
              playsInline
              muted
              className="absolute inset-0 size-full object-cover scale-x-[-1]"
            />
          ) : (
            <img
              src={displaySrc}
              alt=""
              className={cn('size-full object-cover', !previewUrl && 'opacity-90')}
            />
          )}
        </div>

        {!disabled && webcamActive && (
          <button
            type="button"
            title="Capturar foto"
            className="absolute start-1/2 top-1/2 z-10 flex size-10 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-green-600 text-white shadow-md hover:bg-green-700"
            onClick={(e) => {
              e.stopPropagation();
              captureFromWebcam();
            }}
          >
            <Camera className="size-5" />
          </button>
        )}

        {!disabled && (
          <>
            <Button
              type="button"
              variant={webcamActive ? 'default' : 'outline'}
              size="icon"
              title={webcamActive ? 'Encerrar webcam' : 'Abrir webcam'}
              aria-label={webcamActive ? 'Encerrar webcam' : 'Abrir webcam'}
              className="absolute bottom-1 start-4 z-20 size-8 rounded-full shadow-md"
              onClick={() => {
                if (webcamActive) {
                  stopWebcam();
                } else {
                  void startWebcam();
                }
              }}
            >
              <Camera className="size-4" />
            </Button>
            <Button
              type="button"
              variant="outline"
              size="icon"
              title={value ? 'Alterar imagem' : 'Enviar imagem'}
              aria-label={value ? 'Alterar imagem' : 'Enviar imagem'}
              className="absolute -bottom-2 start-1/2 z-20 size-8 -translate-x-1/2 rounded-full shadow-md"
              onClick={() => fileInputRef.current?.click()}
            >
              <Upload className="size-4" />
            </Button>
            <Button
              type="button"
              variant="outline"
              size="icon"
              title={value ? 'Excluir imagem' : 'Editar imagem'}
              aria-label={value ? 'Excluir imagem' : 'Editar imagem'}
              className="absolute bottom-1 end-4 z-20 size-8 rounded-full shadow-md"
              onClick={() => {
                if (value) {
                  handleRemove();
                } else {
                  fileInputRef.current?.click();
                }
              }}
            >
              {value ? <X className="size-4" /> : <Pencil className="size-4" />}
            </Button>
          </>
        )}
      </div>
    </div>
  );
}
