@extends('admin::components.layouts.master')

@section('title', $sermon->title . ' - Administração')

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
@endpush

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $sermon->title }}</h1>
                @if ($sermon->subtitle)
                    <p class="text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2 font-medium italic">
                        <i class="fa-pro fa-solid fa-feather-pointed text-blue-500 text-xs"></i>
                        {{ $sermon->subtitle }}
                    </p>
                @endif
                <div class="flex items-center gap-2 mt-4">
                    <span class="px-3 py-1 text-[10px] font-extrabold rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/20 text-slate-500 dark:text-slate-400 flex items-center gap-1.5 capitalize">
                        <i class="fa-pro fa-solid fa-circle text-[6px]"></i>
                        {{ $sermon->status_display }}
                    </span>
                    <span class="px-3 py-1 text-[10px] font-extrabold rounded-xl border border-blue-100 dark:border-blue-900/30 bg-blue-50 dark:bg-blue-900/10 text-blue-600 dark:text-blue-400 flex items-center gap-1.5 capitalize">
                        <i class="fa-pro fa-solid fa-{{ $sermon->visibility === 'public' ? 'earth-americas' : ($sermon->visibility === 'members' ? 'users' : 'lock') }} text-[8px]"></i>
                        {{ $sermon->visibility_display }}
                    </span>
                    @if ($sermon->is_featured)
                        <span class="px-3 py-1 text-[10px] font-extrabold rounded-xl border border-amber-100 dark:border-amber-900/30 bg-amber-50 dark:bg-amber-900/10 text-amber-600 dark:text-amber-400 flex items-center gap-1.5 uppercase tracking-widest">
                            <i class="fa-pro fa-solid fa-star-sharp text-[8px]"></i>
                            Destaque
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-3" x-data="{ exportModalOpen: false, format: 'full', size: 'a5' }">
                <button type="button" @click="exportModalOpen = true"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-extrabold rounded-2xl shadow-lg shadow-blue-500/20 text-white bg-blue-600 hover:bg-blue-700 transform hover:-translate-y-0.5 transition-all">
                    <i class="fa-pro fa-solid fa-file-pdf mr-2"></i>
                    Exportar para Púlpito
                </button>

                <!-- Export Modal -->
                <div x-show="exportModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-md" @click="exportModalOpen = false"></div>
                    <div class="relative w-full max-w-md rounded-[2rem] bg-white dark:bg-slate-900 shadow-2xl border border-slate-200 dark:border-slate-800 p-8 overflow-hidden transform transition-all" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                            <i class="fa-pro fa-solid fa-print text-blue-500"></i>
                            Configurar Impressão
                        </h3>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Formato do Esboço</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex flex-col items-center gap-2 p-4 rounded-2xl border-2 cursor-pointer transition-all" :class="format === 'full' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'border-slate-100 dark:border-slate-800 text-slate-400 hover:border-slate-200 dark:hover:border-slate-700 bg-slate-50/50 dark:bg-slate-950/20'">
                                        <input type="radio" name="export_format" value="full" x-model="format" class="sr-only">
                                        <i class="fa-pro fa-solid fa-align-left text-lg mb-1"></i>
                                        <span class="text-xs font-bold">Completo</span>
                                    </label>
                                    <label class="flex flex-col items-center gap-2 p-4 rounded-2xl border-2 cursor-pointer transition-all" :class="format === 'topics' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'border-slate-100 dark:border-slate-800 text-slate-400 hover:border-slate-200 dark:hover:border-slate-700 bg-slate-50/50 dark:bg-slate-950/20'">
                                        <input type="radio" name="export_format" value="topics" x-model="format" class="sr-only">
                                        <i class="fa-pro fa-solid fa-list-check text-lg mb-1"></i>
                                        <span class="text-xs font-bold">Tópicos</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Dimensões do Papel</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <button type="button" @click="size = 'a4'" class="flex flex-col items-center gap-3 p-4 rounded-2xl border-2 transition-all" :class="size === 'a4' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/20' : 'border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20'">
                                        <div class="w-10 h-14 rounded-md border-2 border-slate-300 dark:border-slate-700 flex items-center justify-center bg-white dark:bg-slate-800" :class="size === 'a4' ? 'border-blue-400' : ''">
                                            <span class="text-[8px] font-bold text-slate-400" :class="size === 'a4' ? 'text-blue-500' : ''">A4</span>
                                        </div>
                                        <span class="text-[10px] font-extrabold uppercase tracking-wide" :class="size === 'a4' ? 'text-blue-700 dark:text-blue-400' : 'text-slate-500'">Vertical A4</span>
                                    </button>
                                    <button type="button" @click="size = 'a5'" class="flex flex-col items-center gap-3 p-4 rounded-2xl border-2 transition-all" :class="size === 'a5' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/20' : 'border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20'">
                                        <div class="w-14 h-10 rounded-md border-2 border-slate-300 dark:border-slate-700 flex items-center justify-center bg-white dark:bg-slate-800" :class="size === 'a5' ? 'border-blue-400' : ''">
                                            <span class="text-[8px] font-bold text-slate-400" :class="size === 'a5' ? 'text-blue-500' : ''">A5</span>
                                        </div>
                                        <span class="text-[10px] font-extrabold uppercase tracking-wide" :class="size === 'a5' ? 'text-blue-700 dark:text-blue-400' : 'text-slate-500'">Livreto A5</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-8 flex gap-3 justify-end items-center">
                            <button type="button" @click="exportModalOpen = false" class="px-5 py-2.5 text-xs font-extrabold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest">Cancelar</button>
                            <a :href="'{{ route('admin.sermons.sermons.export-pdf', $sermon) }}?format=' + format + '&size=' + size" target="_blank" @click="exportModalOpen = false"
                                class="inline-flex items-center px-8 py-3.5 text-xs font-extrabold text-white bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 rounded-2xl shadow-xl transition-all uppercase tracking-widest">
                                <i class="fa-pro fa-solid fa-file-arrow-down mr-3"></i>
                                Gerar PDF
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.sermons.sermons.edit', $sermon) }}"
                    class="inline-flex items-center px-4 py-2 border border-slate-200 dark:border-slate-800 text-sm font-bold rounded-xl text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                    <i class="fa-pro fa-solid fa-pen-to-square mr-2 text-blue-500"></i>
                    Editar
                </a>
                <a href="{{ route('admin.sermons.sermons.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-slate-200 dark:border-slate-800 text-sm font-bold rounded-xl text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                    <i class="fa-pro fa-solid fa-arrow-left mr-2"></i>
                    Voltar
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <i class="fa-pro fa-solid fa-eye text-6xl"></i>
                </div>
                <p class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Visualizações</p>
                <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white leading-none">{{ number_format($sermon->views) }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <i class="fa-pro fa-solid fa-heart text-6xl"></i>
                </div>
                <p class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Cativações</p>
                <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white leading-none">{{ number_format($sermon->likes) }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <i class="fa-pro fa-solid fa-comments text-6xl"></i>
                </div>
                <p class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Feedbacks</p>
                <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white leading-none">{{ $sermon->comments->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <i class="fa-pro fa-solid fa-handshake text-6xl"></i>
                </div>
                <p class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Co-Autores</p>
                <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white leading-none">{{ $sermon->acceptedCollaborators->count() }}</p>
            </div>
        </div>

        <!-- Bible References -->
        @if ($sermon->bibleReferences->count() > 0)
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">
                <h3 class="text-sm font-extrabold text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                    <i class="fa-pro fa-solid fa-scroll-old text-blue-500"></i>
                    Fundamentação Bíblica
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($sermon->bibleReferences as $ref)
                        <div class="flex items-start gap-4 p-4 bg-slate-50 dark:bg-slate-950/20 rounded-2xl border border-slate-100 dark:border-slate-800 group hover:border-blue-200 dark:hover:border-blue-900/40 transition-all">
                            <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-900 flex items-center justify-center shadow-sm text-blue-500 font-black text-xs border border-slate-100 dark:border-slate-800">
                                {{ $loop->iteration }}
                            </div>
                            <div class="flex-1">
                                <p class="text-[11px] font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-0.5">{{ $ref->type_display }}</p>
                                <p class="text-lg font-extrabold text-slate-900 dark:text-white leading-tight">{{ $ref->formatted_reference }}</p>
                                @if ($ref->context)
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 italic leading-relaxed">{{ $ref->context }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Sermon Content -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-10">
            @if ($sermon->description)
                <div class="mb-10 p-6 rounded-2xl bg-amber-50/50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30">
                    <h3 class="text-[10px] font-extrabold text-amber-600 uppercase tracking-widest mb-2">Resumo Homilético</h3>
                    <p class="text-slate-700 dark:text-slate-300 font-medium italic leading-relaxed">{{ $sermon->description }}</p>
                </div>
            @endif

            @if ($sermon->full_content)
                <div class="mb-12 sermon-content-with-refs">
                    <h3 class="text-sm font-extrabold text-blue-600 uppercase tracking-widest mb-6 flex items-center gap-3">
                        <i class="fa-pro fa-solid fa-pen-nib"></i>
                        Corpo do Manuscrito
                    </h3>
                    <div class="prose prose-slate dark:prose-invert max-w-none text-slate-800 dark:text-slate-200 prose-headings:font-black prose-headings:tracking-tight prose-p:leading-relaxed prose-p:font-serif">
                        {!! $sermon->full_content !!}
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                @if ($sermon->introduction)
                    <div>
                        <h3 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                             <span class="w-6 h-px bg-slate-200"></span> Introdução
                        </h3>
                        <div class="text-slate-700 dark:text-slate-300 leading-relaxed font-serif italic text-lg">
                            {!! nl2br(e($sermon->introduction)) !!}
                        </div>
                    </div>
                @endif

                @if ($sermon->application)
                    <div>
                        <h3 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                             <span class="w-6 h-px bg-slate-200"></span> Aplicação Prática
                        </h3>
                        <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-950/30 border border-slate-100 dark:border-slate-800 text-slate-700 dark:text-slate-300 leading-relaxed">
                            {!! nl2br(e($sermon->application)) !!}
                        </div>
                    </div>
                @endif
            </div>

            @if ($sermon->development)
                <div class="mt-10 pt-10 border-t border-slate-100 dark:border-slate-800">
                    <h3 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                         <span class="w-6 h-px bg-slate-200"></span> Desenvolvimento Teológico
                    </h3>
                    <div class="prose prose-slate dark:prose-invert max-w-none text-lg font-serif">
                        {!! nl2br(e($sermon->development)) !!}
                    </div>
                </div>
            @endif

            @if ($sermon->conclusion)
                <div class="mt-10 pt-10 border-t border-slate-100 dark:border-slate-800">
                    <h3 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2 text-blue-600">
                         <span class="w-6 h-px bg-blue-200"></span> Conclusão & Chamado
                    </h3>
                    <div class="text-slate-700 dark:text-slate-300 leading-relaxed font-bold italic text-xl text-center px-10">
                        {!! nl2br(e($sermon->conclusion)) !!}
                    </div>
                </div>
            @endif
        </div>

        <!-- Tags -->
        @if ($sermon->tags->count() > 0)
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">
                <h3 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-4">Etiquetas Homiléticas</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($sermon->tags as $tag)
                        <span class="px-3 py-1.5 text-[10px] font-extrabold rounded-xl border border-blue-50 dark:border-blue-900/30 bg-blue-50/50 dark:bg-blue-900/10 text-blue-600 dark:text-blue-400 hover:scale-105 transition-transform cursor-default">
                            #{{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Metadata -->
        <!-- Metadata -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">
            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-6">Detalhamento Ministerial</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="flex items-center gap-4">
                    <img src="{{ $sermon->user->avatar_url }}" alt="{{ $sermon->user->name }}" class="h-10 w-10 rounded-full object-cover shadow-sm ring-2 ring-slate-100 dark:ring-slate-800">
                    <div>
                        <dt class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Autor do Esboço</dt>
                        <dd class="text-sm font-bold text-slate-900 dark:text-white">{{ $sermon->user->name }}</dd>
                    </div>
                </div>
                @if ($sermon->category)
                    <div>
                        <dt class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Temática / Categoria</dt>
                        <dd class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 mt-1">
                            <i class="fa-pro fa-solid fa-layer-group text-blue-500 text-xs"></i>
                            {{ $sermon->category->name }}
                        </dd>
                    </div>
                @endif
                @if ($sermon->sermon_date)
                    <div>
                        <dt class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Data da Ministração</dt>
                        <dd class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 mt-1">
                            <i class="fa-pro fa-solid fa-calendar-day text-blue-500 text-xs"></i>
                            {{ $sermon->sermon_date->translatedFormat('d \d\e F, Y') }}
                        </dd>
                    </div>
                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-8 pt-8 border-t border-slate-100 dark:border-slate-800">
                <div>
                    <dt class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">Publicação Oficial</dt>
                    <dd class="text-[11px] font-bold text-slate-600 dark:text-slate-400 mt-1">
                        {{ $sermon->published_at ? $sermon->published_at->format('d/m/Y H:i') : 'Rascunho' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">Data de Registro</dt>
                    <dd class="text-[11px] font-bold text-slate-600 dark:text-slate-400 mt-1">{{ $sermon->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">Última Revisão</dt>
                    <dd class="text-[11px] font-bold text-slate-600 dark:text-slate-400 mt-1">{{ $sermon->updated_at->format('d/m/Y H:i') }}</dd>
                </div>
            </div>
        </div>
    </div>

    @if ($sermon->full_content)
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
@endsection
