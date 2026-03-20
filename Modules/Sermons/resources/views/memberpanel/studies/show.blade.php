@extends('memberpanel::components.layouts.master')

@section('title', $study->title)

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
    <style>
        .study-content {
            font-family: 'Merriweather', 'Georgia', serif;
            line-height: 1.9;
            font-size: 1.125rem;
        }
        .study-content h2 {
            font-family: 'Inter', sans-serif;
            margin-top: 2.5em;
            margin-bottom: 1em;
            font-weight: 800;
            letter-spacing: -0.025em;
        }
        .study-content h3 {
             font-family: 'Inter', sans-serif;
            margin-top: 2em;
            margin-bottom: 0.75em;
            font-weight: 700;
             letter-spacing: -0.025em;
        }
        .study-content p { margin-bottom: 1.5em; }
        .study-content ul { list-style-type: disc; padding-left: 1.5em; margin-bottom: 1.5em; }
        .study-content blockquote {
            border-left: 4px solid #10B981;
            padding-left: 1.5rem;
            font-style: italic;
            color: #4B5563;
            margin: 2em 0;
            background: #F0FDF4;
            padding: 1.5rem;
            border-radius: 0 0.5rem 0.5rem 0;
        }
        .dark .study-content blockquote {
            background: rgba(16, 185, 129, 0.1);
            color: #D1D5DB;
        }
    </style>
@endpush

@section('content')
@php
    $parsedStudyContent = app(\Modules\Bible\App\Services\BibleReferenceParserService::class)->parseText($study->content);
    preg_match_all('/data-bible-ref="([^"]+)"/', $parsedStudyContent, $refMatches);
    $studyReferences = collect($refMatches[1] ?? [])->map(fn ($r) => trim($r))->filter()->unique()->values();

    $globalDefaultAbbr = \App\Models\Settings::get('default_bible_version_abbreviation', '');
    $defaultBibleVersion = \Modules\Bible\App\Models\BibleVersion::query()
        ->where('is_active', true)
        ->when($globalDefaultAbbr, fn ($q) => $q->whereRaw('LOWER(abbreviation) = ?', [strtolower($globalDefaultAbbr)]))
        ->first();

    if (! $defaultBibleVersion) {
        $defaultBibleVersion = \Modules\Bible\App\Models\BibleVersion::default()->first()
            ?? \Modules\Bible\App\Models\BibleVersion::active()->first();
    }

    $panoramaBooks = \Modules\Bible\App\Models\Book::query()
        ->select('id', 'name', 'book_number')
        ->when($defaultBibleVersion, fn ($q) => $q->where('bible_version_id', $defaultBibleVersion->id))
        ->whereNotNull('book_number')
        ->orderBy('book_number')
        ->get();
@endphp
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    <!-- Breadcrumb -->
    <nav class="flex text-sm font-medium" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="{{ route('memberpanel.sermon-outlines.index') }}" class="text-gray-500 hover:text-emerald-600 dark:text-gray-400 dark:hover:text-emerald-400 transition-colors">Esboços</a>
            </li>
            <li class="text-gray-300 dark:text-gray-600">/</li>
            @if($study->series)
                <li>
                    <a href="{{ route('memberpanel.sermon-series.show', $study->series) }}" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300">
                        {{ $study->series->title }}
                    </a>
                </li>
                <li class="text-gray-300 dark:text-gray-600">/</li>
            @endif
             <li class="text-gray-900 dark:text-gray-200 truncate max-w-xs" aria-current="page">{{ $study->title }}</li>
        </ol>
    </nav>

    <article class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <header class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 relative overflow-hidden">
             <!-- Cover Image Hero -->
            @if($study->cover_image)
                <div class="h-64 md:h-80 w-full relative">
                    <img src="{{ asset('storage/' . $study->cover_image) }}" alt="Capa do estudo {{ $study->title }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-linear-to-t from-gray-50 dark:from-gray-900 via-transparent to-transparent"></div>
                </div>
            @endif

            <div class="p-8 md:p-16 relative z-10 {{ $study->cover_image ? '-mt-24' : '' }}">

            <div class="relative z-10">
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    @if($study->category)
                        <span class="inline-flex px-3 py-1 rounded-lg text-xs font-black uppercase tracking-widest"
                            style="background-color: {{ $study->category->color ?? '#10B981' }}20; color: {{ $study->category->color ?? '#10B981' }}">
                            {{ $study->category->name }}
                        </span>
                    @endif
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-black uppercase tracking-widest bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                         {{ ceil(str_word_count(strip_tags($study->content)) / 200) }} min de leitura
                    </span>
                </div>

                <h1 class="text-4xl md:text-6xl font-black text-gray-900 dark:text-white mb-6 leading-tight tracking-tight">
                    {{ $study->title }}
                </h1>

                @if($study->subtitle)
                    <p class="text-xl md:text-2xl text-gray-500 dark:text-gray-400 font-medium leading-relaxed max-w-3xl">
                        {{ $study->subtitle }}
                    </p>
                @endif

                <div class="flex flex-wrap items-center gap-8 mt-8 pt-8 border-t border-gray-200 dark:border-gray-700/50">
                    <div class="flex items-center gap-3">
                        <img src="{{ $study->user->avatar_url ?? $study->user->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="{{ $study->user->name }}" class="w-12 h-12 rounded-full object-cover shadow-sm">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Autor</p>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $study->user->name }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                         <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                            <x-icon name="calendar" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Publicado</p>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $study->published_at?->translatedFormat('d \\d\\e M \\d\\e Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Media Section -->
        @if($study->video_url || $study->audio_file || $study->audio_url)
        <div class="bg-gray-900 border-b border-gray-800">
             @if($study->video_url)
            <div class="aspect-w-16 aspect-h-9 mx-auto max-w-5xl">
                @if(str_contains($study->video_url, 'youtube.com') || str_contains($study->video_url, 'youtu.be'))
                    @php
                        $videoId = '';
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $study->video_url, $matches)) {
                            $videoId = $matches[1];
                        }
                    @endphp
                    <iframe src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full"></iframe>
                @else
                    <div class="flex items-center justify-center h-full text-white bg-gray-800">
                        <a href="{{ $study->video_url }}" target="_blank" class="flex flex-col items-center gap-4 hover:text-emerald-400 transition-colors group p-8 text-center">
                            <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                 <x-icon name="play" class="w-8 h-8" />
                            </div>
                            <span class="font-bold text-lg">Assistir Vídeo</span>
                        </a>
                    </div>
                @endif
            </div>
            @endif

            @if($study->audio_file || $study->audio_url)
                <div class="p-6 bg-gray-800/50 backdrop-blur-sm border-t border-gray-700">
                    <div class="flex items-center gap-4 max-w-3xl mx-auto">
                    <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center shrink-0 text-white shadow-lg shadow-emerald-500/20 animate-pulse">
                            <x-icon name="volume-up" class="w-6 h-6" />
                        </div>
                        <div class="flex-1">
                             <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">Versão em Áudio</p>
                            <audio controls class="w-full h-10 rounded-lg" controlsList="nodownload">
                                <source src="{{ $study->audio_source }}" type="audio/mpeg">
                                <source src="{{ $study->audio_source }}" type="audio/mp4">
                                Seu navegador não suporta o elemento de áudio.
                            </audio>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @endif

        @if($studyReferences->count() > 0)
            <section class="px-8 md:px-16 pt-10">
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-6 dark:border-emerald-900/40 dark:bg-emerald-900/10">
                    <h3 class="mb-4 text-xs font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-300">Base Bíblica do Estudo</h3>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        @foreach($studyReferences as $reference)
                            <a href="{{ route('memberpanel.bible.search', ['q' => $reference]) }}"
                                class="rounded-xl border border-emerald-200 bg-white px-4 py-3 text-sm font-bold text-emerald-700 transition hover:-translate-y-0.5 hover:shadow-sm dark:border-emerald-900/50 dark:bg-slate-900 dark:text-emerald-300"
                                target="_blank" rel="noopener noreferrer">
                                {{ $reference }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section x-data="studyBibleContext()" class="px-8 md:px-16 pt-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-900">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Painel de Contexto Bíblico</h3>
                    <span x-show="loading" aria-live="polite" class="text-xs font-bold text-amber-600">Carregando...</span>
                </div>
                @if($defaultBibleVersion)
                    <p class="mb-3 text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                        Versão padrão aplicada: <span class="text-slate-700 dark:text-slate-200">{{ $defaultBibleVersion->name }} ({{ $defaultBibleVersion->abbreviation }})</span>
                    </p>
                @endif
                <select x-model="selectedBookNumber" @change="fetchPanorama()" aria-label="Selecionar livro para visualizar panorama bíblico" class="w-full rounded-xl border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    <option value="">Selecione um livro para panorama</option>
                    @foreach($panoramaBooks as $book)
                        <option value="{{ $book->book_number }}">{{ $book->name }}</option>
                    @endforeach
                </select>

                <template x-if="panorama">
                    <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Autor</p>
                            <p class="mt-1 text-sm text-slate-700 dark:text-slate-200" x-text="panorama.author || '—'"></p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Data</p>
                            <p class="mt-1 text-sm text-slate-700 dark:text-slate-200" x-text="panorama.date_written || '—'"></p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800 md:col-span-2">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Tema central</p>
                            <p class="mt-1 text-sm text-slate-700 dark:text-slate-200" x-text="panorama.theme_central || '—'"></p>
                        </div>
                    </div>
                </template>
            </div>
        </section>

        <section x-data="studyNotes('{{ $study->id }}')" class="px-8 md:px-16 pt-8">
            <div class="rounded-2xl border border-amber-100 bg-amber-50/60 p-6 dark:border-amber-900/40 dark:bg-amber-900/10">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-widest text-amber-700 dark:text-amber-300">Anotações de estudo</h3>
                    <button type="button" @click="clearNotes()" class="text-xs font-bold text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">Limpar</button>
                </div>
                <textarea x-model="content" @input.debounce.400ms="save()"
                    rows="6"
                    aria-label="Anotações pessoais do estudo"
                    placeholder="Escreva aqui seus insights, aplicações práticas e pontos para compartilhar..."
                    class="w-full rounded-xl border-amber-200 bg-white text-sm leading-relaxed text-slate-700 dark:border-amber-900/40 dark:bg-slate-900 dark:text-slate-200"></textarea>
                <p class="mt-2 text-[11px] font-medium text-slate-500 dark:text-slate-400">As notas ficam salvas neste navegador para continuar seu estudo depois.</p>
            </div>
        </section>

        <!-- Content Body -->
        <div class="p-8 md:p-16 study-content text-gray-800 dark:text-gray-200">
            {!! nl2br($parsedStudyContent) !!}
        </div>

        <!-- Footer -->
        <div class="px-8 md:px-16 pb-12 pt-8 border-t border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-6 bg-gray-50/30 dark:bg-gray-800">
            <a href="{{ route('memberpanel.sermon-outlines.index') }}"
               class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl font-bold text-gray-700 dark:text-white shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                Voltar aos Esboços
            </a>

             @if($study->series)
                <a href="{{ route('memberpanel.sermon-series.show', $study->series) }}"
                   class="inline-flex items-center px-6 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-xl font-bold text-emerald-700 dark:text-emerald-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                    Mais da série "{{ Str::limit($study->series->title, 20) }}"
                    <x-icon name="arrow-right" class="w-4 h-4 ml-2" />
                </a>
            @endif
        </div>
    </article>
</div>

<script>
    function studyBibleContext() {
        return {
            selectedBookNumber: '',
            panorama: null,
            loading: false,
            async fetchPanorama() {
                if (!this.selectedBookNumber) {
                    this.panorama = null;
                    return;
                }

                this.loading = true;
                try {
                    const response = await fetch('/api/v1/bible/panorama?book_number=' + encodeURIComponent(this.selectedBookNumber));
                    const json = await response.json();
                    this.panorama = response.ok ? (json.data || null) : null;
                } catch (error) {
                    this.panorama = null;
                }
                this.loading = false;
            }
        };
    }

    function studyNotes(studyId) {
        const key = 'vepl_study_notes_' + studyId;
        return {
            content: localStorage.getItem(key) || '',
            save() {
                localStorage.setItem(key, this.content || '');
            },
            clearNotes() {
                this.content = '';
                localStorage.removeItem(key);
            }
        };
    }
</script>
@endsection

