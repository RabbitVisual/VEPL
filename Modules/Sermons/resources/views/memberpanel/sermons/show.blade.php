@extends('memberpanel::components.layouts.master')

@section('title', $sermon->title)

@push('styles')
<style>
    /* Typography Customization for "Medium" feel */
    .prose-sermon {
        font-family: 'Merriweather', 'Georgia', serif;
        font-size: 1.125rem;
        line-height: 1.8;
        color: #292929;
    }
    .dark .prose-sermon {
        color: #d1d5db;
    }
    .prose-sermon h2, .prose-sermon h3 {
        font-family: 'Inter', sans-serif;
        font-weight: 800;
        margin-top: 2em;
        margin-bottom: 0.5em;
        color: #111827;
    }
    .dark .prose-sermon h2, .dark .prose-sermon h3 {
        color: #f3f4f6;
    }
    .prose-sermon blockquote {
        border-left: 4px solid #f59e0b; /* Amber */
        padding-left: 1.5rem;
        font-style: italic;
        background: #fffbeb; /* Amber-50 */
        padding: 1rem;
        border-radius: 0.5rem;
        margin: 2rem 0;
    }
    .dark .prose-sermon blockquote {
        background: rgba(245, 158, 11, 0.1);
        color: #d1d5db;
    }

    /* Tooltip */
    .bible-tooltip {
        position: absolute;
        background: #1f2937;
        color: white;
        padding: 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        max-width: 300px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 50;
        display: none;
        border: 1px solid #374151;
    }

    .bible-ref-link {
        color: #f59e0b;
        text-decoration: none;
        border-bottom: 1px dotted #f59e0b;
        cursor: pointer;
        font-weight: 600;
    }
    .bible-ref-link:hover {
        background: rgba(245, 158, 11, 0.1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-white dark:bg-slate-950 pb-32">
    <!-- Navbar Placeholder (if not in master layout) -->

    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
        <!-- Header -->
        <header class="mb-10 text-center">
            <div class="mb-6">
                @if($sermon->category)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-500">
                        {{ $sermon->category->name }}
                    </span>
                @endif
            </div>

            <h1 class="text-2xl sm:text-4xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight leading-tight mb-4">
                {{ $sermon->title }}
            </h1>

            @if($sermon->subtitle)
                <p class="text-xl text-gray-500 dark:text-gray-400 font-medium leading-relaxed">
                    {{ $sermon->subtitle }}
                </p>
            @endif

            <!-- Meta/Author -->
            <div class="mt-8 flex items-center justify-center space-x-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-800 overflow-hidden">
                        @if($sermon->user->avatar_url || $sermon->user->profile_photo_url)
                            <img src="{{ $sermon->user->avatar_url ?? $sermon->user->profile_photo_url }}" class="w-full h-full object-cover">
                        @else
                            <x-icon name="user" class="w-6 h-6 text-gray-400 m-2" />
                        @endif
                    </div>
                    <div class="ml-3 text-left">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $sermon->user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $sermon->published_at?->translatedFormat('d \\d\\e M \\d\\e Y') }} • {{ ceil(str_word_count(strip_tags($sermon->full_content ?? $sermon->development)) / 200) }} min leitura</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Cover Image -->
        @if($sermon->cover_image)
            <div class="mb-12 rounded-2xl overflow-hidden shadow-2xl">
                <img src="{{ asset('storage/' . $sermon->cover_image) }}" alt="{{ $sermon->title }}" class="w-full h-auto object-cover max-h-[500px]">
            </div>
        @endif

        @php
            $bibleRefs = app(\Modules\Bible\App\Services\BibleReferenceParserService::class);
        @endphp

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Visualizações</p>
                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($sermon->views) }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Curtidas</p>
                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($sermon->likes) }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Interações</p>
                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-white">{{ $sermon->comments->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Coautores</p>
                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-white">{{ $sermon->acceptedCollaborators->count() }}</p>
            </div>
        </div>

        @if($sermon->bibleReferences->count() > 0)
            <section class="mt-12 mb-10 rounded-2xl border border-slate-200 bg-slate-50 p-6 dark:border-slate-800 dark:bg-slate-900/40">
                <h3 class="mb-4 text-sm font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Fundamentação Bíblica</h3>
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    @foreach($sermon->bibleReferences as $reference)
                        <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                            <p class="text-[10px] font-black uppercase tracking-widest text-amber-600">{{ $reference->type_display }}</p>
                            <p class="mt-1 text-base font-extrabold text-slate-900 dark:text-slate-100">{{ $reference->formatted_reference }}</p>
                            @if($reference->context)
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $reference->context }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if(($contextBooks ?? collect())->count() > 0)
            <section x-data="sermonBibleContext()" class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-900">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Contexto Bíblico</h3>
                    <span x-show="loading" class="text-xs font-bold text-amber-600">Carregando...</span>
                </div>
                <div class="mb-4">
                    <select x-model="selectedBookNumber" @change="fetchPanorama()" aria-label="Selecionar livro para contexto bíblico" class="w-full rounded-xl border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                        <option value="">Selecione o livro</option>
                        @foreach($contextBooks as $book)
                            <option value="{{ $book->book_number }}">{{ $book->name }}</option>
                        @endforeach
                    </select>
                </div>
                <template x-if="panorama">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Autor</p>
                            <p class="mt-1 text-sm font-medium text-slate-700 dark:text-slate-200" x-text="panorama.author || '—'"></p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Data</p>
                            <p class="mt-1 text-sm font-medium text-slate-700 dark:text-slate-200" x-text="panorama.date_written || '—'"></p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800 md:col-span-2">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Tema Central</p>
                            <p class="mt-1 text-sm font-medium text-slate-700 dark:text-slate-200" x-text="panorama.theme_central || '—'"></p>
                        </div>
                    </div>
                </template>
            </section>
        @endif

        <!-- Sermon Content (Admin-like complete reading) -->
        <section class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">
            @if ($sermon->description)
                <div class="mb-8 rounded-2xl border border-amber-100 bg-amber-50/60 p-5 dark:border-amber-900/30 dark:bg-amber-900/10">
                    <h3 class="text-[10px] font-extrabold uppercase tracking-widest text-amber-700 dark:text-amber-400 mb-2">Resumo homilético</h3>
                    <div class="text-slate-700 dark:text-slate-300 italic leading-relaxed">{!! $bibleRefs->parseText($sermon->description) !!}</div>
                </div>
            @endif

            @if ($sermon->full_content)
                <div class="mb-10 sermon-content-with-refs prose-sermon" id="sermon-content">
                    {!! $bibleRefs->parseText($sermon->full_content) !!}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @if ($sermon->introduction)
                    <div>
                        <h4 class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-slate-400 mb-3">Introdução</h4>
                        <div class="text-slate-700 dark:text-slate-300 leading-relaxed">{!! $bibleRefs->parseText($sermon->introduction) !!}</div>
                    </div>
                @endif

                @if ($sermon->practical_application)
                    <div>
                        <h4 class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-slate-400 mb-3">Aplicação prática</h4>
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-slate-700 dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-300">{!! $bibleRefs->parseText($sermon->practical_application) !!}</div>
                    </div>
                @endif
            </div>

            @if ($sermon->body_outline)
                <div class="mt-8 border-t border-slate-100 pt-8 dark:border-slate-800">
                    <h4 class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-slate-400 mb-3">Esboço e desenvolvimento</h4>
                    <div class="prose prose-slate dark:prose-invert max-w-none">{!! $bibleRefs->parseText($sermon->body_outline) !!}</div>
                </div>
            @endif

            @if ($sermon->conclusion)
                <div class="mt-8 border-t border-slate-100 pt-8 dark:border-slate-800">
                    <h4 class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-blue-600 mb-3">Conclusão e chamado</h4>
                    <div class="text-slate-700 dark:text-slate-300 leading-relaxed font-semibold">{!! $bibleRefs->parseText($sermon->conclusion) !!}</div>
                </div>
            @endif
        </section>

        @if($sermon->full_content)
        <script>
            (function() {
                var baseUrl = @json(route('memberpanel.bible.search'));
                document.querySelectorAll('.sermon-content-with-refs .bible-ref').forEach(function(el) {
                    var ref = el.getAttribute('data-bible-ref');
                    if (!ref) return;
                    var wrap = document.createElement('div');
                    wrap.className = 'mt-2';
                    var link = document.createElement('a');
                    link.href = baseUrl + '?q=' + encodeURIComponent(ref);
                    link.textContent = 'Ver na Bíblia';
                    link.className = 'text-sm text-amber-600 dark:text-amber-400 hover:underline';
                    link.target = '_blank';
                    wrap.appendChild(link);
                    el.appendChild(wrap);
                });
            })();
        </script>
        @endif

        <!-- Tags & Actions -->
        <div class="mt-16 pt-8 border-t border-gray-100 dark:border-gray-800">
            <div class="flex flex-wrap gap-2 mb-8">
                @foreach($sermon->tags as $tag)
                    <a href="{{ route('memberpanel.sermons.index', ['tag_id' => $tag->id]) }}" class="text-sm text-gray-500 hover:text-amber-500 dark:text-gray-400 dark:hover:text-amber-400 transition-colors">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>

            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex flex-wrap items-center gap-4">
                    <form action="{{ route('memberpanel.sermons.toggle-favorite', $sermon->id) }}" method="POST">
                        @csrf
                        <button type="submit" aria-label="{{ $isFavorite ? 'Remover dos salvos' : 'Salvar sermão' }}" class="flex items-center space-x-2 min-h-[44px] min-w-[44px] py-2 pr-2 rounded-lg {{ $isFavorite ? 'text-amber-500' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300' }} transition-colors touch-manipulation">
                            <x-icon name="{{ $isFavorite ? 'star' : 'star' }}" class="w-6 h-6 {{ $isFavorite ? 'fill-current' : '' }}" />
                            <span class="text-sm font-bold">{{ $isFavorite ? 'Salvo' : 'Salvar' }}</span>
                        </button>
                    </form>

                    <button type="button" aria-label="Compartilhar sermão" class="flex items-center space-x-2 min-h-[44px] py-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors touch-manipulation">
                        <x-icon name="share" class="w-6 h-6" />
                        <span class="text-sm font-bold">Compartilhar</span>
                    </button>
                </div>

                @if($canEdit)
                    <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route('memberpanel.sermons.edit', $sermon) }}" class="inline-flex items-center min-h-[44px] py-2 text-sm font-bold text-blue-500 hover:text-blue-600 touch-manipulation">
                        Editar Sermão
                    </a>
                    <div x-data="{ exportModalOpen: false, format: 'full', size: 'a5', openExport() { this.exportModalOpen = true; this.$nextTick(() => this.$refs.closeBtn?.focus()); }, closeExport() { this.exportModalOpen = false; this.$nextTick(() => this.$refs.openBtn?.focus()); } }" class="contents">
                        <button type="button" x-ref="openBtn" @click="openExport()" class="text-sm font-bold text-amber-600 hover:text-amber-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                            Exportar para púlpito
                        </button>
                        <div x-show="exportModalOpen" x-cloak x-on:keydown.escape.window="closeExport()" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
                            <div @click.outside="closeExport()" x-ref="dialog" role="dialog" aria-modal="true" aria-labelledby="export-dialog-title" class="w-full max-w-md rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                                <h3 id="export-dialog-title" class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <x-icon name="file-pdf" class="w-5 h-5 text-amber-500" />
                                    Exportar para o púlpito
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Formato</label>
                                        <div class="flex gap-3">
                                            <label class="flex-1 flex items-center justify-center gap-2 p-3 rounded-lg border-2 cursor-pointer transition-colors" :class="format === 'full' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                                                <input type="radio" name="member_export_format" value="full" x-model="format" class="sr-only">
                                                <span class="text-sm font-medium">Esboço completo</span>
                                            </label>
                                            <label class="flex-1 flex items-center justify-center gap-2 p-3 rounded-lg border-2 cursor-pointer transition-colors" :class="format === 'topics' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                                                <input type="radio" name="member_export_format" value="topics" x-model="format" class="sr-only">
                                                <span class="text-sm font-medium">Apenas tópicos</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tamanho do papel</label>
                                        <div class="flex gap-4">
                                            <label class="flex-1 cursor-pointer" @click="size = 'a4'">
                                                <div class="rounded-lg border-2 p-2 transition-colors text-center" :class="size === 'a4' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600'">
                                                    <div class="mx-auto rounded bg-gray-200 dark:bg-gray-600" style="width: 42px; height: 59px;"></div>
                                                    <span class="block text-xs font-bold mt-1 text-gray-700 dark:text-gray-300">A4</span>
                                                </div>
                                            </label>
                                            <label class="flex-1 cursor-pointer" @click="size = 'a5'">
                                                <div class="rounded-lg border-2 p-2 transition-colors text-center" :class="size === 'a5' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-600'">
                                                    <div class="mx-auto rounded bg-gray-200 dark:bg-gray-600" style="width: 30px; height: 42px;"></div>
                                                    <span class="block text-xs font-bold mt-1 text-gray-700 dark:text-gray-300">A5</span>
                                                </div>
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Proporção visual do documento.</p>
                                    </div>
                                </div>
                                <div class="mt-6 flex gap-3 justify-end">
                                    <button type="button" x-ref="closeBtn" @click="closeExport()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">Cancelar</button>
                                    <a :href="'{{ route('memberpanel.sermons.export-pdf', $sermon) }}?format=' + format + '&size=' + size" target="_blank" @click="closeExport()"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                                        <x-icon name="download" class="w-4 h-4 mr-2" />
                                        Gerar PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                @endif
            </div>
        </div>

        <section class="mt-12 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-900">
            <h3 class="mb-4 text-lg font-extrabold text-slate-900 dark:text-slate-100">Comentários e Interações</h3>
            <form action="{{ route('memberpanel.sermons.store-comment', $sermon) }}" method="POST" class="mb-6 space-y-3">
                @csrf
                <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                    <select name="type" aria-label="Tipo de interação" class="rounded-xl border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                        <option value="comment">Comentário</option>
                        <option value="question">Pergunta</option>
                        <option value="feedback">Feedback</option>
                        <option value="suggestion">Sugestão</option>
                    </select>
                    <textarea name="comment" required rows="3" aria-label="Escrever comentário principal" placeholder="Contribua com sua observação pastoral..." class="md:col-span-3 rounded-xl border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">{{ old('comment') }}</textarea>
                </div>
                <button class="rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-amber-700">Enviar interação</button>
            </form>

            <div class="space-y-4">
                @forelse($sermon->comments as $comment)
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/60">
                        <div class="mb-2 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <img src="{{ $comment->user->avatar_url ?? $comment->user->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="Avatar de {{ $comment->user->name }}" class="h-7 w-7 rounded-full object-cover">
                                <p class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $comment->user->name }}</p>
                                <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ $comment->type_display }}</span>
                            </div>
                            <span class="text-[11px] font-medium text-slate-500">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-slate-700 dark:text-slate-200">{{ $comment->comment }}</p>

                        <form action="{{ route('memberpanel.sermons.store-comment', $sermon) }}" method="POST" class="mt-3 flex items-start gap-2">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <input type="hidden" name="type" value="comment">
                            <textarea name="comment" rows="1" aria-label="Responder comentário de {{ $comment->user->name }}" placeholder="Responder..." class="flex-1 rounded-lg border-slate-300 text-xs dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"></textarea>
                            <button class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-bold text-white hover:bg-slate-700 dark:bg-slate-200 dark:text-slate-900">Responder</button>
                        </form>

                        @if($comment->replies->count() > 0)
                            <div class="mt-3 space-y-2 border-l-2 border-slate-200 pl-3 dark:border-slate-700">
                                @foreach($comment->replies as $reply)
                                    <div class="rounded-lg bg-white p-3 text-sm dark:bg-slate-900">
                                        <div class="mb-1 flex items-center gap-2">
                                            <img src="{{ $reply->user->avatar_url ?? $reply->user->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="Avatar de {{ $reply->user->name }}" class="h-6 w-6 rounded-full object-cover">
                                            <p class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $reply->user->name }}</p>
                                            <span class="text-[10px] text-slate-500">{{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-xs text-slate-700 dark:text-slate-200">{{ $reply->comment }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </article>
                @empty
                    <p class="text-sm text-slate-500">Nenhum comentário ainda. Seja o primeiro a contribuir.</p>
                @endforelse
            </div>
        </section>

        <!-- Metadata -->
        <section class="mt-10 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="mb-5 text-sm font-extrabold text-slate-900 dark:text-white">Detalhamento ministerial</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Autor</p>
                    <p class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">{{ $sermon->user->name }}</p>
                </div>
                @if($sermon->category)
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Categoria</p>
                        <p class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">{{ $sermon->category->name }}</p>
                    </div>
                @endif
                @if($sermon->sermon_date)
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Data da ministração</p>
                        <p class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">{{ $sermon->sermon_date->translatedFormat('d/m/Y') }}</p>
                    </div>
                @endif
            </div>
            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Publicação</p>
                    <p class="mt-1 text-xs font-bold text-slate-600 dark:text-slate-300">{{ $sermon->published_at ? $sermon->published_at->format('d/m/Y H:i') : 'Rascunho' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Criado em</p>
                    <p class="mt-1 text-xs font-bold text-slate-600 dark:text-slate-300">{{ $sermon->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Atualizado em</p>
                    <p class="mt-1 text-xs font-bold text-slate-600 dark:text-slate-300">{{ $sermon->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </section>

        <!-- Attachments -->
        @if(!empty($sermon->attachments))
            <div class="mt-12 bg-gray-50 dark:bg-gray-900 rounded-xl p-6 border border-gray-100 dark:border-gray-800">
                <h4 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <x-icon name="paper-clip" class="w-5 h-5 mr-2" />
                    Materiais de Apoio
                </h4>
                <div class="grid gap-3">
                    @foreach($sermon->attachments as $file)
                        <a href="{{ Storage::url($file['path']) }}" target="_blank" class="flex items-center p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-amber-500 dark:hover:border-amber-500 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-500 flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold uppercase">{{ pathinfo($file['name'], PATHINFO_EXTENSION) }}</span>
                            </div>
                            <div class="ml-4 overflow-hidden">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate group-hover:text-amber-500 transition-colors">{{ $file['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($file['size'] / 1024, 1) }} KB</p>
                            </div>
                            <x-icon name="download" class="w-5 h-5 ml-auto text-gray-400 group-hover:text-amber-500" />
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Worship Suggestion -->
        @if($sermon->worshipSuggestion)
            <div class="mt-8 bg-slate-900 rounded-xl p-6 text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-linear-to-r from-amber-600/20 to-purple-600/20"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-1">Sugestão de Louvor</p>
                        <h4 class="text-xl font-bold">{{ $sermon->worshipSuggestion->title }}</h4>
                        @if($sermon->worshipSuggestion->artist)
                            <p class="text-sm text-gray-400">{{ $sermon->worshipSuggestion->artist }}</p>
                        @endif
                    </div>
                    @if($sermon->worshipSuggestion->youtube_url)
                        <a href="{{ $sermon->worshipSuggestion->youtube_url }}" target="_blank" rel="noopener noreferrer" class="p-3 bg-white/10 hover:bg-white/20 rounded-full transition-colors">
                            <x-icon name="play" class="w-6 h-6 text-white" />
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </main>

    <!-- Audio Player Footer (Sticky) -->
    <!-- Assuming we have an audio_url field or attachment. Since schema didn't explicitly add audio_url in the prompt list but asked for player, I'll check if exists.
         Actually, the prompt said "Player de Áudio (se houver podcast associado)".
         I'll assume 'attachments' might contain audio or 'audio_url' exists.
         Checking migration: 'audio_url' was added to bible_commentaries, not sermons.
         But let's assume if an attachment is mp3, we play it.
    -->
    @php
        $audioFile = null;
        if(!empty($sermon->attachments)) {
            foreach($sermon->attachments as $file) {
                if(str_contains($file['mime'], 'audio')) {
                    $audioFile = Storage::url($file['path']);
                    break;
                }
            }
        }
    @endphp

    @if($audioFile)
        <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 p-4 z-50 shadow-lg">
            <div class="max-w-3xl mx-auto flex items-center gap-4">
                <button id="play-pause-btn" type="button" class="w-12 h-12 rounded-full bg-amber-500 hover:bg-amber-600 text-white flex items-center justify-center shrink-0 transition-colors" aria-label="Reproduzir">
                    <span id="play-icon" class="flex items-center justify-center"><x-icon name="play" class="w-6 h-6 ml-1" /></span>
                    <span id="pause-icon" class="hidden"><x-icon name="pause" class="w-6 h-6" /></span>
                </button>
                <div class="flex-1">
                    <p class="text-xs font-bold text-amber-500 uppercase tracking-widest">Ouvir Sermão</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $sermon->title }}</p>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1 mt-2 overflow-hidden">
                        <div id="progress-bar" class="bg-amber-500 h-full w-0 transition-all duration-100"></div>
                    </div>
                </div>
                <audio id="sermon-audio" src="{{ $audioFile }}"></audio>
            </div>
        </div>
    @endif

    <!-- Tooltip Element -->
    <div id="bible-tooltip" class="bible-tooltip"></div>
</div>

<script>
    function sermonBibleContext() {
        return {
            selectedBookNumber: '',
            panorama: null,
            loading: false,
            async fetchPanorama() {
                if (!this.selectedBookNumber) { this.panorama = null; return; }
                this.loading = true;
                const res = await fetch('/api/v1/bible/panorama?book_number=' + encodeURIComponent(this.selectedBookNumber));
                const json = await res.json();
                this.panorama = res.ok ? (json.data || null) : null;
                this.loading = false;
            }
        };
    }

    document.addEventListener('DOMContentLoaded', () => {

        // Audio Player Logic
        const audio = document.getElementById('sermon-audio');
        if (audio) {
            const btn = document.getElementById('play-pause-btn');
            const progress = document.getElementById('progress-bar');

            const playIcon = document.getElementById('play-icon');
            const pauseIcon = document.getElementById('pause-icon');
            btn.addEventListener('click', () => {
                if (audio.paused) {
                    audio.play();
                    if (playIcon) playIcon.classList.add('hidden');
                    if (pauseIcon) { pauseIcon.classList.remove('hidden'); pauseIcon.classList.add('flex', 'items-center', 'justify-center'); }
                    btn.setAttribute('aria-label', 'Pausar');
                } else {
                    audio.pause();
                    if (playIcon) playIcon.classList.remove('hidden');
                    if (pauseIcon) { pauseIcon.classList.add('hidden'); pauseIcon.classList.remove('flex', 'items-center', 'justify-center'); }
                    btn.setAttribute('aria-label', 'Reproduzir');
                }
            });

            audio.addEventListener('timeupdate', () => {
                const percent = (audio.currentTime / audio.duration) * 100;
                progress.style.width = percent + '%';
            });
        }

        // Bible Tooltip Logic
        const content = document.getElementById('sermon-content');
        const tooltip = document.getElementById('bible-tooltip');
        if (!content || !tooltip) return;

        // Simple regex to find bible refs (e.g., Joao 3:16) and wrap them
        // Note: Replacing HTML can break event listeners attached to existing elements, but since this is a read view it's mostly safe.
        // For robustness, we target text nodes.

        // This is a simplified regex.
        const bibleRegex = /\b((?:[1-3]\s)?[A-Z][a-zçáéíóúâêôãõ]+)\s(\d+):(\d+(?:-\d+)?)\b/g;

        function linkifyBibleRefs(node) {
            if (node.nodeType === 3) { // Text node
                const text = node.nodeValue;
                if (bibleRegex.test(text)) {
                    const span = document.createElement('span');
                    span.innerHTML = text.replace(bibleRegex, '<span class="bible-ref-link" data-ref="$&">$&</span>');
                    node.parentNode.replaceChild(span, node);
                }
            } else if (node.nodeType === 1 && node.nodeName !== 'A' && node.nodeName !== 'SCRIPT' && !node.classList.contains('bible-ref-link')) {
                node.childNodes.forEach(linkifyBibleRefs);
            }
        }

        // Run linker
        linkifyBibleRefs(content);

        // Event delegation for tooltips
        content.addEventListener('mouseover', (e) => {
            if (e.target.classList.contains('bible-ref-link')) {
                const ref = e.target.getAttribute('data-ref');
                showTooltip(e.target, ref);
            }
        });

        content.addEventListener('mouseout', (e) => {
            if (e.target.classList.contains('bible-ref-link')) {
                tooltip.style.display = 'none';
            }
        });

        const verseCache = {};
        function showTooltip(element, ref) {
            const rect = element.getBoundingClientRect();
            tooltip.style.left = (rect.left + window.scrollX) + 'px';
            tooltip.style.top = (rect.bottom + window.scrollY + 5) + 'px';
            tooltip.style.display = 'block';
            tooltip.innerHTML = `<strong>${ref}</strong><br><span class="text-gray-300 italic">Carregando texto bíblico...</span>`;

            if (verseCache[ref]) {
                tooltip.innerHTML = verseCache[ref];
                return;
            }

            fetch(`/api/v1/bible/find?ref=${encodeURIComponent(ref)}`, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(resp => {
                    if (resp.data && resp.data.verses && resp.data.verses.length) {
                        const reference = resp.data.reference || ref;
                        const text = resp.data.verses.map(v => v.text).join(' ');
                        verseCache[ref] = `<strong>${reference}</strong><br><span class="text-gray-300">${text}</span>`;
                        tooltip.innerHTML = verseCache[ref];
                    } else {
                        tooltip.innerHTML = `<strong>${ref}</strong><br><span class="text-gray-400 italic">Referência não encontrada.</span>`;
                    }
                })
                .catch(() => {
                    tooltip.innerHTML = `<strong>${ref}</strong><br><span class="text-gray-400 italic">Erro ao carregar versículo.</span>`;
                });
        }
    });
</script>
@endsection

