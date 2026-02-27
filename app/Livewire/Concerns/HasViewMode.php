<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Session;

trait HasViewMode
{
    /**
     * O modo de visualização atual.
     */
    public string $viewMode = 'list';

    /**
     * Inicializa o modo de visualização.
     */
    public function mountHasViewMode()
    {
        $this->viewMode = Session::get('viewMode', $this->getDefaultViewMode());
    }

    /**
     * Atualiza o modo de visualização no componente e na sessão.
     * 
     * @param string $mode
     */
    public function updatedViewMode($value)
    {
        Session::put('viewMode', $value);
    }

    /**
     * Obtém o modo de visualização padrão.
     * No mobile é 'grid', caso contrário é 'list'.
     * 
     * @return string
     */
    protected function getDefaultViewMode(): string
    {
        if ($this->isMobile()) {
            return 'grid';
        }

        return 'list';
    }

    /**
     * Detecta se o acesso é via dispositivo móvel.
     * 
     * @return bool
     */
    protected function isMobile(): bool
    {
        $userAgent = request()->header('User-Agent', '');
        return (bool) preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $userAgent);
    }
}
