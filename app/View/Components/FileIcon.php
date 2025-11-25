<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FileIcon extends Component
{
    public $file;
    public $anexo;
    
    /**
     * Create a new component instance.
     */
    public function __construct($file = null, $anexo = null)
    {
        // Se passar o objeto anexo, usa ele; senão usa o file (compatibilidade com código antigo)
        if ($anexo) {
            $this->anexo = $anexo;
            $this->file = $anexo->caminho_arquivo ?? $anexo->link ?? null;
        } else {
            $this->file = $file;
            $this->anexo = null;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('app.components.file-icon');
    }

    public function getIconClass()
    {
        // Se for um link, retorna ícone de link
        if ($this->anexo && ($this->anexo->forma_anexo ?? 'arquivo') === 'link') {
            return 'bi-link-45deg';
        }
        
        // Se não tiver arquivo, retorna ícone padrão
        if (!$this->file) {
            return 'bi-file-earmark-fill';
        }
        
        $extension = strtolower(pathinfo($this->file, PATHINFO_EXTENSION));
        $icons = [
            'pdf' => 'bi-file-earmark-pdf-fill',
            'jpg' => 'bi bi-file-earmark-image',
            'jpeg' => 'bi bi-file-earmark-image',
            'png' => 'bi bi-file-earmark-image',
            'doc' => 'bi-file-earmark-word-fill',
            'docx' => 'bi-file-earmark-word-fill',
            'xls' => 'bi-file-earmark-spreadsheet-fill',
            'xlsx' => 'bi-file-earmark-spreadsheet-fill',
            'txt' => 'bi-file-earmark-text-fill',
        ];
        return $icons[$extension] ?? 'bi-file-earmark-fill';
    }
    
    public function getHref()
    {
        // Se for um link, retorna o link diretamente
        if ($this->anexo && ($this->anexo->forma_anexo ?? 'arquivo') === 'link') {
            return $this->anexo->link ?? '#';
        }
        
        // Se for arquivo e tiver caminho, gera a rota
        if ($this->file && $this->anexo && ($this->anexo->forma_anexo ?? 'arquivo') === 'arquivo') {
            return route('file', ['path' => $this->file]);
        }
        
        // Fallback para compatibilidade com código antigo
        if ($this->file) {
            return route('file', ['path' => $this->file]);
        }
        
        return '#';
    }
    
    public function getDisplayName()
    {
        // Se for um link, mostra o link
        if ($this->anexo && ($this->anexo->forma_anexo ?? 'arquivo') === 'link') {
            return $this->anexo->link ?? 'Link';
        }
        
        // Se for arquivo, mostra o nome do arquivo
        if ($this->anexo && $this->anexo->nome_arquivo) {
            return $this->anexo->nome_arquivo;
        }
        
        // Fallback para compatibilidade
        if ($this->file) {
            return basename($this->file);
        }
        
        return 'Arquivo';
    }
}
