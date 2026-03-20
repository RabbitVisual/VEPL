<?php

namespace Modules\Bible\App\Concerns;

use Modules\Bible\App\Services\BibleReferenceParserService;

/**
 * Use em Models ou helpers para transformar texto bruto em HTML com citações clicáveis.
 */
trait HasBibleReferences
{
    public function parseBibleReferences(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        return app(BibleReferenceParserService::class)->parseText($text);
    }
}
