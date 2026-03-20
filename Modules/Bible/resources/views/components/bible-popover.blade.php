{{-- Popover global: requer Alpine.data('biblePopover') em resources/js/app.js --}}
<div x-data="biblePopover()" class="contents">
    <div
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bible-reference-popover fixed z-50 max-h-[min(70vh,560px)] w-[min(420px,92vw)] overflow-hidden rounded-xl border border-white/10 bg-slate-900 text-slate-100 shadow-xl"
        :class="centered ? 'left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2' : ''"
        :style="centered ? '' : `top:${pos.top};left:${pos.left};`"
        role="dialog"
        aria-modal="true"
        @mouseenter="onPopoverEnter()"
        @mouseleave="onPopoverLeave()"
    >
        <div class="flex items-start justify-between gap-2 border-b border-white/10 px-4 py-3">
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-amber-400" x-text="data?.reference || 'Referência'"></p>
                <p class="truncate text-xs text-slate-400" x-show="data?.book" x-text="data?.book"></p>
            </div>
            <button
                type="button"
                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-white/10 hover:text-slate-100"
                @click="close()"
                aria-label="Fechar"
            >
                <span class="sr-only">Fechar</span>
                <x-icon name="xmark" style="duotone" class="text-lg" />
            </button>
        </div>

        <div class="px-4 pt-3">
            <div class="flex gap-1 rounded-lg bg-slate-800/80 p-1 text-xs font-medium">
                <button
                    type="button"
                    class="flex-1 rounded-md px-2 py-1.5 transition"
                    :class="tab === 'text' ? 'bg-amber-500/20 text-amber-300' : 'text-slate-400 hover:text-slate-200'"
                    @click="tab = 'text'"
                >Texto</button>
                <button
                    type="button"
                    class="flex-1 rounded-md px-2 py-1.5 transition"
                    :class="tab === 'strongs' ? 'bg-amber-500/20 text-amber-300' : 'text-slate-400 hover:text-slate-200'"
                    @click="tab = 'strongs'"
                >Original / Strong</button>
                <button
                    type="button"
                    class="flex-1 rounded-md px-2 py-1.5 transition"
                    :class="tab === 'context' ? 'bg-amber-500/20 text-amber-300' : 'text-slate-400 hover:text-slate-200'"
                    @click="tab = 'context'"
                >Contexto</button>
            </div>
        </div>

        <div class="max-h-[min(52vh,440px)] overflow-y-auto px-4 py-4">
            <template x-if="loading">
                <div class="space-y-3" aria-busy="true">
                    <div class="h-4 w-3/4 animate-pulse rounded bg-slate-700/80"></div>
                    <div class="h-4 w-full animate-pulse rounded bg-slate-700/60"></div>
                    <div class="h-4 w-5/6 animate-pulse rounded bg-slate-700/60"></div>
                    <div class="h-24 w-full animate-pulse rounded-lg bg-slate-800/80"></div>
                    <div class="h-4 w-2/3 animate-pulse rounded bg-slate-700/60"></div>
                </div>
            </template>

            <template x-if="!loading && error">
                <p class="text-sm text-red-300" x-text="error"></p>
            </template>

            <template x-if="!loading && !error && data && tab === 'text'">
                <div class="space-y-3 text-sm leading-relaxed text-slate-100">
                    <p class="whitespace-pre-wrap" x-text="data.text"></p>
                    <a
                        x-show="data.full_chapter_url"
                        :href="data.full_chapter_url"
                        class="inline-flex items-center gap-2 text-amber-400 hover:text-amber-300"
                    >
                        <span>Abrir capítulo completo</span>
                        <x-icon name="arrow-up-right-from-square" style="duotone" class="text-xs" />
                    </a>
                </div>
            </template>

            <template x-if="!loading && !error && data && tab === 'strongs'">
                <div class="space-y-3">
                    <template x-if="!data.original_language || data.original_language.length === 0">
                        <p class="text-sm text-slate-400">Interlinear / Strong não disponível para este trecho.</p>
                    </template>
                    <template x-if="data.original_language && data.original_language.length">
                        <ul class="space-y-3 text-sm">
                            <template x-for="(row, idx) in data.original_language" :key="idx">
                                <li class="rounded-lg border border-white/5 bg-slate-800/50 p-3">
                                    <div class="flex flex-wrap items-baseline gap-2 text-xs text-slate-400">
                                        <span x-text="'#' + row.position"></span>
                                        <span class="font-mono text-slate-300" x-text="row.word_surface"></span>
                                        <span x-show="row.strong_number" x-text="row.strong_number"></span>
                                        <span x-show="row.lang" x-text="row.lang === 'he' ? 'Hebraico' : 'Grego'"></span>
                                    </div>
                                    <template x-if="row.lexicon">
                                        <div class="mt-2 space-y-1 text-sm text-slate-200">
                                            <p x-show="row.lexicon.lemma_br || row.lexicon.lemma">
                                                <span class="text-slate-500">Lemma: </span>
                                                <span x-text="row.lexicon.lemma_br || row.lexicon.lemma"></span>
                                            </p>
                                            <p x-show="row.lexicon.description_pt" class="text-slate-300" x-text="row.lexicon.description_pt"></p>
                                        </div>
                                    </template>
                                    <template x-if="!row.lexicon && row.strong_number">
                                        <p class="mt-2 text-xs text-slate-500">Entrada do léxico não encontrada para <span x-text="row.strong_number"></span>.</p>
                                    </template>
                                </li>
                            </template>
                        </ul>
                    </template>
                </div>
            </template>

            <template x-if="!loading && !error && data && tab === 'context'">
                <div class="space-y-4 text-sm">
                    <template x-if="data.panorama">
                        <div class="space-y-2 rounded-lg border border-white/5 bg-slate-800/40 p-3">
                            <p x-show="data.panorama.author"><span class="text-slate-500">Autor: </span><span x-text="data.panorama.author"></span></p>
                            <p x-show="data.panorama.date_written"><span class="text-slate-500">Data: </span><span x-text="data.panorama.date_written"></span></p>
                            <p x-show="data.panorama.theme_central" class="whitespace-pre-wrap"><span class="text-slate-500">Tema: </span><span x-text="data.panorama.theme_central"></span></p>
                            <p x-show="data.panorama.recipients" class="whitespace-pre-wrap"><span class="text-slate-500">Destinatários: </span><span x-text="data.panorama.recipients"></span></p>
                        </div>
                    </template>
                    <template x-if="!data.panorama">
                        <p class="text-slate-400">Panorama do livro ainda não cadastrado.</p>
                    </template>
                    <div class="rounded-lg border border-dashed border-white/10 p-3 text-slate-500">
                        <p class="font-medium text-slate-400">Comentário oficial</p>
                        <p class="mt-1 text-sm">Em breve — conteúdo teológico aprovado pela plataforma.</p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
