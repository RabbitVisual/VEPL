@extends('admin::components.layouts.master')

@section('title', 'Editar Comentário - Administração')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
<!-- Page Header -->
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Editar Exegese</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
            <x-icon name="pen-nib" style="solid" class="text-blue-500" />
            Refinando Comentários: {{ $commentary->reference_display }}
        </p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.sermons.commentaries.index') }}"
            class="inline-flex items-center px-4 py-2 border border-slate-200 dark:border-slate-800 text-sm font-bold rounded-xl text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm">
            <x-icon name="arrow-left" style="solid" class="mr-2" />
            Voltar
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-sm border border-slate-200 dark:border-slate-800 p-8">
            <form action="{{ route('admin.sermons.commentaries.update', $commentary) }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="commentaryForm">
                @csrf
                @method('PUT')

                <!-- Bible Reference Section -->
                <div class="space-y-6 bg-slate-50 dark:bg-slate-950/30 rounded-3xl p-6 border border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                        <x-icon name="book-bible" style="solid" class="text-blue-500" />
                        Referência Bíblica Atual
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="bible_version_id" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Versão</label>
                            <select name="bible_version_id" id="bible_version_id" required
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 text-xs font-bold transition-all">
                                @foreach($bibleVersions as $version)
                                    <option value="{{ $version->id }}" {{ $selectedVersionId == $version->id ? 'selected' : '' }}>{{ $version->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="book_id" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Livro</label>
                            <select name="book_id" id="book_id" required data-selected="{{ $selectedBookId }}"
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 text-xs font-bold transition-all">
                                @foreach($bibleBooks as $book)
                                    <option value="{{ $book->id }}" {{ $selectedBookId == $book->id ? 'selected' : '' }}>{{ $book->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="chapter_id" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Capítulo</label>
                            <select name="chapter_id" id="chapter_id" required data-selected="{{ $selectedChapterId }}"
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 text-xs font-bold transition-all">
                                <option value="">Carregando...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Versículos</label>
                            <div class="relative">
                                <input type="text" name="verses" readonly required
                                    data-selected-verses="{{ $versesString }}"
                                    value="{{ $versesString }}"
                                    class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 text-xs font-bold py-2.5 cursor-pointer pr-10"
                                    placeholder="Selecionar...">
                                <x-icon name="list-ol" style="solid" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
                            <x-icon name="feather-pointed" style="solid" class="text-slate-400" />
                            Cabeçalho do Comentário
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title', $commentary->title) }}"
                            class="block w-full rounded-2xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm font-bold py-3 transition-all"
                            placeholder="Título representativo">
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                            <x-icon name="quote-left" style="solid" class="text-blue-500" />
                            Corpo da Exegese
                        </label>
                        <textarea name="content" id="content" rows="12" required
                            class="block w-full rounded-2xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all">{{ old('content', $commentary->content) }}</textarea>
                    </div>
                </div>

                <!-- Media Section -->
                <div class="border-t border-slate-100 dark:border-slate-800 pt-8 mt-4">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                        <x-icon name="waveform-lines" style="solid" class="text-blue-500" />
                        Apoio em Áudio
                    </h3>

                    <input type="hidden" name="remove_audio" id="remove_audio" value="0">

                    @if($commentary->audio_path || $commentary->audio_url)
                        <div id="audio-current-container" class="mb-8 p-6 bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 rounded-[2rem]">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest bg-blue-100 dark:bg-blue-900/40 px-3 py-1 rounded-full">Áudio Ativo</span>
                                <button type="button" onclick="if(confirm('Remover o áudio permanentemente?')) { document.getElementById('remove_audio').value = '1'; document.getElementById('audio-current-container').style.display='none'; }"
                                    class="text-red-500 hover:text-red-700 text-[10px] font-black uppercase tracking-widest flex items-center gap-2 transition-all">
                                    <x-icon name="trash-can" style="solid" />
                                    Remover
                                </button>
                            </div>
                            <audio controls class="w-full h-10">
                                <source src="{{ $commentary->audio_source }}" type="audio/mpeg">
                                Seu navegador não suporta áudio.
                            </audio>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="audio_url" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Novo Link Externo</label>
                            <input type="url" name="audio_url" id="audio_url" value="{{ old('audio_url', $commentary->audio_url) }}"
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 text-xs font-bold transition-all"
                                placeholder="Spotify, Podcast URL...">
                        </div>
                        <div>
                            <label for="audio" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Substituir por Upload</label>
                            <input type="file" name="audio" id="audio" accept="audio/*"
                                class="block w-full text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:bg-slate-900 file:text-white hover:file:bg-slate-800 transition-all cursor-pointer">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar Lateral -->
    <div class="space-y-6">
        <!-- Publicação -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center justify-between">
                <span>Controle</span>
                <x-icon name="sliders" style="solid" />
            </h3>

            <div class="space-y-6">
                <div>
                    <label for="status" class="block text-xs font-bold text-slate-500 mb-2">Estado</label>
                    <select name="status" id="status" form="commentaryForm" required
                        class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 text-sm font-bold transition-all">
                        <option value="draft" {{ old('status', $commentary->status) == 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="published" {{ old('status', $commentary->status) == 'published' ? 'selected' : '' }}>Publicado</option>
                    </select>
                </div>

                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-950/30 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Comentário Oficial</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_official" value="1" form="commentaryForm" class="sr-only peer" {{ old('is_official', $commentary->is_official) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none dark:bg-slate-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" form="commentaryForm"
                    class="w-full py-4 bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white font-black rounded-2xl shadow-xl shadow-blue-500/10 transform hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                    <x-icon name="arrows-rotate" style="solid" />
                    ATUALIZAR EXEGESE
                </button>
            </div>
        </div>

        <!-- Capa/Imagem -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 text-center">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center justify-between">
                <span>Miniatura</span>
                <x-icon name="image" style="solid" />
            </h3>

            <div class="relative group mx-auto w-full aspect-square rounded-[2rem] bg-slate-50 dark:bg-slate-950 border-2 border-dashed border-slate-200 dark:border-slate-800 flex flex-col items-center justify-center overflow-hidden transition-all hover:border-blue-500/50">
                <div id="cover-preview" class="absolute inset-0 z-0">
                    @if($commentary->cover_image)
                        <img src="{{ asset('storage/' . $commentary->cover_image) }}" class="w-full h-full object-cover">
                    @else
                        <x-icon name="microscope" style="solid" class="text-4xl text-slate-300 dark:text-slate-800 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" />
                    @endif
                </div>
                <div class="relative z-10 p-4 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button type="button" onclick="document.getElementById('cover_image_file').click()"
                        class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white p-3 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 transform hover:scale-110 transition-all">
                        <x-icon name="camera" style="solid" />
                    </button>
                </div>
                <input type="file" name="cover_image_file" id="cover_image_file" form="commentaryForm" accept="image/*" class="hidden" onchange="previewCover(event)">
            </div>

            <div class="mt-4 flex flex-col items-center gap-2">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Apoio Visual</p>
                @if($commentary->cover_image)
                    <label class="flex items-center text-[10px] text-red-500 font-black uppercase tracking-widest cursor-pointer hover:text-red-600 transition-colors">
                        <input type="checkbox" name="remove_cover" value="1" form="commentaryForm" class="mr-2 h-3 w-3 rounded-full border-slate-300 text-red-600 focus:ring-red-500">
                        Remover existente
                    </label>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@push('scripts')
<script>
    function previewCover(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('cover-preview');
            preview.innerHTML = `<img src="${reader.result}" class="w-full h-full object-cover">`;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
    @vite(['Modules/Sermons/resources/assets/js/app.js'])
@endpush
@endsection
