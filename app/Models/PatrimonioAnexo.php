<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatrimonioAnexo extends Model
{
    use HasFactory;

    protected $fillable = [
        'patrimonio_id',
        'nome_arquivo',
        'caminho_arquivo',
        'tipo_arquivo',
        'tamanho_arquivo',
        'descricao',
        'uploaded_by',
    ];

    /**
     * Relacionamento com Patrimonio.
     * Cada anexo pertence a um único patrimônio.
     */
    public function patrimonio()
    {
        return $this->belongsTo(Patrimonio::class);
    }

    /**
     * Relacionamento com User.
     * Cada anexo foi feito por um usuário (opcional).
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

        // Adiciona o relacionamento com o usuário que fez a última atualização
        public function updatedBy()
        {
            return $this->belongsTo(User::class, 'updated_by');
        }

        // Método para formatar o tamanho do arquivo
        public function getFormattedFileSizeAttribute()
        {
            $size = $this->tamanho_arquivo;
            if ($size >= 1073741824) {
                return number_format($size / 1073741824, 2) . ' GB';
            } elseif ($size >= 1048576) {
                return number_format($size / 1048576, 2) . ' MB';
            } elseif ($size >= 1024) {
                return number_format($size / 1024, 2) . ' KB';
            } else {
                return $size . ' bytes';
            }
        }

        // Método para formatar a data de atualização
        public function getFormattedUpdatedAtAttribute()
        {
            return $this->updated_at->format('d M Y, g:i a');
        }

        public function getFileIcon()
        {
            // Extrair a extensão do arquivo
            $extension = strtolower(pathinfo($this->nome_arquivo, PATHINFO_EXTENSION));

            // Mapeamento de extensões para ícones
            $icons = [
                'png'  => '/assets/media/svg/files/png.svg',
                'jpeg' => '/assets/media/svg/files/fil_image.svg',
                'jpg'  => '/assets/media/svg/files/jpg.svg',
                'gif'  => '/assets/media/svg/files/gif.svg',
                'pdf'  => '/assets/media/svg/files/pdf.svg',
                'doc'  => '/assets/media/svg/files/doc.svg',
                'docx' => '/assets/media/svg/files/docx.svg',
                'zip'  => '/assets/media/svg/files/zip.svg',
                'rar'  => '/assets/media/svg/files/fil_zip.svg',
                // Extensão padrão para arquivos desconhecidos
                'default' => '/assets/media/svg/files/default_file.svg', // Ícone padrão
            ];


            // Retorna o ícone baseado na extensão, ou o ícone padrão
            return $icons[$extension] ?? $icons['default'];
        }
}
