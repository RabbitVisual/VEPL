{{-- Painel Elias no editor de sermão: Sugerir Ilustração, Verificar Coerência, Pesquisa Histórica. --}}
@php
    $chatUrl = route('memberpanel.cbav-bot.chat');
@endphp
<div x-data="eliasSermonStudio('{{ $chatUrl }}')" class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
    <button @click="open = !open" class="w-full px-5 py-4 flex items-center justify-between text-left font-extrabold text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
        <span class="flex items-center gap-3">
            <i class="fa-pro fa-solid fa-sparkles text-blue-500"></i>
            Elias – Mentor Homilético
            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 text-[10px] font-bold cursor-help" title="Consultoria baseada na Declaração de Fé da CBB e princípios homiléticos de Isaltino Coelho.">?</span>
        </span>
        <i class="fa-pro fa-solid fa-chevron-down text-[10px] transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="border-t border-slate-100 dark:border-slate-800">
        <div class="p-5 space-y-4">
            <div class="flex items-start gap-3 p-4 rounded-2xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/30">
                <i class="fa-pro fa-solid fa-wand-magic-sparkles text-blue-500 mt-0.5"></i>
                <p class="text-[11px] text-blue-800 dark:text-blue-300 leading-relaxed font-medium">
                    <strong>Sugestão Elias:</strong> Use a revisão antes de imprimir. Ela organiza a hierarquia de parágrafos sem alterar sua doutrina.
                </p>
            </div>

            <div class="flex flex-col gap-2">
                <button type="button" @click="askElias('revise_format')" :disabled="loading"
                    class="text-left px-4 py-2.5 rounded-xl bg-slate-900 dark:bg-blue-600 text-white text-xs font-bold hover:bg-slate-800 dark:hover:bg-blue-700 disabled:opacity-50 transition-all shadow-sm flex items-center gap-3">
                    <i class="fa-pro fa-solid fa-align-left text-[10px]"></i>
                    Formatar Manuscrito para Púlpito
                </button>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="askElias('suggest_illustration')" :disabled="loading"
                        class="text-left px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950/30 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-bold hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-50 transition-all flex items-center gap-2">
                        <i class="fa-pro fa-solid fa-lightbulb text-blue-500"></i>
                        Ilustrar Ponto
                    </button>
                    <button type="button" @click="askElias('historical_research')" :disabled="loading"
                        class="text-left px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950/30 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-bold hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-50 transition-all flex items-center gap-2">
                        <i class="fa-pro fa-solid fa-monument text-blue-500"></i>
                        Contexto Histórico
                    </button>
                </div>
                <button type="button" @click="askElias('check_coherence')" :disabled="loading"
                    class="text-left px-4 py-2.5 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 text-xs font-bold hover:border-blue-400 hover:text-blue-600 dark:hover:text-blue-400 transition-all flex items-center gap-3">
                    <i class="fa-pro fa-solid fa-shield-check text-[10px]"></i>
                    Verificar Coerência Doutrinária (CBB)
                </button>
            </div>

            <template x-if="loading">
                <div class="flex items-center gap-3 py-2 text-blue-600 animate-pulse">
                    <i class="fa-pro fa-solid fa-spinner-third animate-spin"></i>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest">Elias está analisando...</span>
                </div>
            </template>

            <template x-if="reply">
                <div class="mt-2 space-y-3">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/50 text-[13px] text-slate-700 dark:text-slate-300 whitespace-pre-wrap border border-slate-100 dark:border-slate-800 leading-relaxed font-serif italic" x-text="reply"></div>
                    <template x-if="formattedHtml">
                        <button type="button" @click="applyFormat()"
                            class="w-full inline-flex items-center justify-center gap-3 px-5 py-3 text-xs font-extrabold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg shadow-blue-500/20 transition-all transform hover:-translate-y-0.5">
                            <i class="fa-pro fa-solid fa-check-double"></i>
                            Aplicar Melhorias ao Texto
                        </button>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function eliasSermonStudio(chatUrl) {
        return {
            open: true,
            loading: false,
            reply: '',
            formattedHtml: null,
            async askElias(action) {
                this.loading = true;
                this.reply = '';
                this.formattedHtml = null;
                const mainPoint = (document.getElementById('title')?.value || '').trim();
                const editorEl = document.querySelector('.ql-editor');
                const excerpt = editorEl ? (editorEl.innerText || '').replace(/\s+/g, ' ').trim().slice(0, 1500) : '';
                const fullContent = editorEl ? (editorEl.innerHTML || '') : '';
                const reference = (document.querySelector('[name="bible_references[0][reference_text]"]')?.value || '').trim()
                    || (editorEl?.innerText?.slice(0, 200) || '');
                const defaultMsg = action === 'suggest_illustration' ? (mainPoint || excerpt.slice(0, 100) || 'ponto principal do sermão')
                    : (action === 'historical_research' ? (reference || 'contexto do texto') : (action === 'revise_format' ? 'Formatar sermão para o púlpito.' : 'verificar coerência CBB'));
                const payload = {
                    message: defaultMsg,
                    context: {
                        sermon_studio: true,
                        action: action,
                        main_point: mainPoint,
                        reference: reference,
                        excerpt: excerpt,
                        full_content: action === 'revise_format' ? fullContent : undefined
                    }
                };
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                try {
                    const res = await fetch(chatUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    if (res.ok) {
                        this.reply = data.reply || 'Sem resposta.';
                        if (data.formatted_html) this.formattedHtml = data.formatted_html;
                    } else {
                        this.reply = data.message || data.errors?.message?.[0] || 'Resposta inválida. Tente de novo.';
                    }
                } catch (e) {
                    this.reply = 'Erro ao conectar com o Elias. Verifica a ligação e tenta outra vez.';
                }
                this.loading = false;
            },
            applyFormat() {
                if (!this.formattedHtml) return;
                window.dispatchEvent(new CustomEvent('elias-apply-format', { detail: { html: this.formattedHtml } }));
                this.formattedHtml = null;
                this.reply = 'Formatação aplicada. O conteúdo do editor foi atualizado para impressão no púlpito.';
            }
        };
    }
</script>
