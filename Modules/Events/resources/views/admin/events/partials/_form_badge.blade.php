{{-- Partial: _form_badge.blade.php — Badge/Credencial config (edit only) --}}
@php $ev = $event ?? null; @endphp

@php
    $ev = $event ?? null;
    $badge = $ev?->badges()?->first();
    $badgeTemplates = \Modules\Events\App\Models\EventBadge::templates();
    $currentTemplateHtml = $badge?->template_html;
    $currentTemplateKey = null;
    if ($currentTemplateHtml) {
        foreach ($badgeTemplates as $tpl) {
            if (trim($tpl['html']) === trim((string) $currentTemplateHtml)) {
                $currentTemplateKey = $tpl['id'];
                break;
            }
        }
    }
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-700 mb-5">
        <div class="w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0">
            <x-icon name="id-card-clip" style="duotone" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
        </div>
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Credencial (Badge)</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Configure o modelo de credencial para impressão</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label for="badge_orientation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Orientação</label>
            <select name="badge_orientation" id="badge_orientation"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                <option value="portrait"  {{ old('badge_orientation', $badge?->orientation ?? 'portrait') === 'portrait'  ? 'selected' : '' }}>Retrato</option>
                <option value="landscape" {{ old('badge_orientation', $badge?->orientation ?? 'portrait') === 'landscape' ? 'selected' : '' }}>Paisagem</option>
            </select>
        </div>
        <div>
            <label for="badge_paper_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tamanho do Papel</label>
            <select name="badge_paper_size" id="badge_paper_size"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                @foreach(['A4' => 'A4', 'Letter' => 'Letter'] as $v => $l)
                    <option value="{{ $v }}" {{ old('badge_paper_size', $badge?->paper_size ?? 'A4') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="badge_per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Credenciais por Página</label>
            <select name="badge_per_page" id="badge_per_page"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                @foreach([4, 6, 8, 10] as $n)
                    <option value="{{ $n }}" {{ old('badge_per_page', $badge?->per_page ?? 6) == $n ? 'selected' : '' }}>{{ $n }} por página</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-3 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="flex-1">
                    <label for="badge_template_preset" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('events::messages.badge_template_preset') ?? 'Estilo do crachá' }}
                    </label>
                    <select id="badge_template_preset" name="badge_template_preset"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                        <option value="">{{ __('events::messages.badge_template_preset_custom') ?? 'Personalizado / manter HTML atual' }}</option>
                        @foreach($badgeTemplates as $tpl)
                            <option value="{{ $tpl['id'] }}"
                                data-template="{{ e($tpl['html']) }}"
                                data-description="{{ e($tpl['description']) }}"
                                {{ old('badge_template_preset', $currentTemplateKey) === $tpl['id'] ? 'selected' : '' }}>
                                {{ $tpl['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <p id="badge_template_preset_description" class="text-xs text-gray-500 dark:text-gray-400 sm:w-1/2">
                    {{ $currentTemplateKey
                        ? ($badgeTemplates[array_search($currentTemplateKey, array_column($badgeTemplates, 'id'))]['description'] ?? '')
                        : __('events::messages.badge_template_preset_help') }}
                </p>
            </div>

            <label for="badge_template_html" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Template HTML da Credencial
                <span class="text-xs text-gray-400 font-normal ml-1">
                    — use {{ '{' }}{{ ' name ' }}{{ '}' }}, {{ '{' }}{{ ' event ' }}{{ '}' }}, {{ '{' }}{{ ' role ' }}{{ '}' }}, {{ '{' }}{{ ' date ' }}{{ '}' }}, {{ '{' }}{{ ' location ' }}{{ '}' }} e {{ '{' }}{{ ' qr_code ' }}{{ '}' }}
                </span>
            </label>
            <textarea name="badge_template_html" id="badge_template_html" rows="5"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm font-mono text-xs">{{ old('badge_template_html', $badge?->template_html) }}</textarea>
            {{-- Preview simples do crachá --}}
            <div class="mt-2 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                    Pré-visualização rápida (exemplo). Use \"Imprimir Crachás\" na tela de inscrições para ver o PDF final em tamanho real.
                </p>
                @php
                    $previewTemplate = $badge?->template_html ?: \Modules\Events\App\Models\EventBadge::getDefaultTemplate();
                    $previewHtml = app(\Modules\Events\App\Services\BadgePdfService::class)->parseTemplate($previewTemplate, [
                        'name' => 'João da Silva',
                        'event' => \Illuminate\Support\Str::limit($ev?->title ?? 'Nome do Evento', 30),
                        'role' => 'Participante',
                        'date' => optional($ev?->start_date)->format('d/m/Y') ?? '10/10/2026',
                        'location' => \Illuminate\Support\Str::limit($ev?->location ?? 'Local do Evento', 25),
                        'qr_code' => '',
                    ]);
                @endphp
                <div id="badge_preview_container" class="bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 p-3 text-xs">
                    {!! $previewHtml !!}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('badge_template_preset');
    const textarea = document.getElementById('badge_template_html');
    const descEl = document.getElementById('badge_template_preset_description');
    const preview = document.getElementById('badge_preview_container');
    if (!select || !textarea || !preview) return;

    function decodeHtml(value) {
        if (!value) return '';
        return value
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>')
            .replace(/&amp;/g, '&')
            .replace(/&#039;/g, "'")
            .replace(/&quot;/g, '"');
    }

    function buildPreviewHtml(template) {
        if (!template) return '';
        let html = template;
        // placeholders novos
        html = html.replace(/\{\{\s*name\s*\}\}/g, 'João da Silva');
        html = html.replace(/\{\{\s*event\s*\}\}/g, 'Nome do Evento');
        html = html.replace(/\{\{\s*role\s*\}\}/g, 'Participante');
        html = html.replace(/\{\{\s*date\s*\}\}/g, '10/10/2026');
        html = html.replace(/\{\{\s*location\s*\}\}/g, 'Local do Evento');
        html = html.replace(/\{\{\s*qr_code\s*\}\}/g, '');
        // compatibilidade com placeholders antigos
        html = html.replace(/\{\{\s*nome\s*\}\}/g, 'João da Silva');
        html = html.replace(/\{\{\s*evento\s*\}\}/g, 'Nome do Evento');
        html = html.replace(/\{\{\s*funcao\s*\}\}/g, 'Participante');
        html = html.replace(/\{\{\s*data\s*\}\}/g, '10/10/2026');
        html = html.replace(/\{\{\s*local\s*\}\}/g, 'Local do Evento');
        return html;
    }

    select.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const template = decodeHtml(opt.getAttribute('data-template') || '');
        const description = opt.getAttribute('data-description') || '';

        if (template) {
            textarea.value = template;
            preview.innerHTML = buildPreviewHtml(template);
        }

        if (descEl) {
            descEl.textContent = description || 'Escolha um modelo pronto para preencher o HTML abaixo automaticamente. Depois você pode ajustar detalhes se desejar.';
        }
    });

    textarea.addEventListener('input', function () {
        preview.innerHTML = buildPreviewHtml(this.value);
    });
});
</script>
@endpush
</div>
