<?php

namespace Modules\Intercessor\App\Services;

use Modules\Bible\App\Services\BibleReferenceParserService;

/**
 * Ponte de compatibilidade: delega ao motor central de citações do módulo Bible.
 *
 * @see BibleReferenceParserService
 */
class BibleParser
{
    /**
     * Converte referências bíblicas reconhecíveis em botões `.bible-reference-link`
     * (popover global no painel de membros). Preserva tags HTML; analisa apenas
     * segmentos de texto com base nos nomes/abreviações da versão padrão.
     */
    public static function parse(string $text): string
    {
        return app(BibleReferenceParserService::class)->parseText($text);
    }
}
