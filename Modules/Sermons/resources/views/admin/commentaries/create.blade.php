@extends('admin::components.layouts.master')

@section('title', 'Novo Comentário - Administração')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
<!-- Page Header -->
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Nova Exegese Bíblica</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
            <x-icon name="microscope" style="solid" class="text-blue-500" />
            Gestão de Comentários Versículo a Versículo
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
            <form action="{{ route('admin.sermons.commentaries.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="commentaryForm">
                @csrf

                <!-- Bible Reference Section -->
                <div class="space-y-6 bg-slate-50 dark:bg-slate-950/30 rounded-3xl p-6 border border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                        <x-icon name="book-bible" style="solid" class="text-blue-500" />
                        Referência Exegética
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="bible_version_id" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Versão</label>
                            <select name="bible_version_id" id="bible_version_id" required
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 text-xs font-bold transition-all">
                                <option value="">Versão...</option>
                                @foreach($bibleVersions as $version)
                                    <option value="{{ $version->id }}">{{ $version->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="book_id" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Livro</label>
                            <select name="book_id" id="book_id" required
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 text-xs font-bold transition-all">
                                <option value="">Aguardando...</option>
                            </select>
                        </div>
                        <div>
                            <label for="chapter_id" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Capítulo</label>
                            <select name="chapter_id" id="chapter_id" required
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 text-xs font-bold transition-all">
                                <option value="">Capítulo...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Versículos</label>
                            <div class="relative">
                                <input type="text" name="verses" readonly required
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
                            Cabeçalho do Comentário (Opcional)
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                            class="block w-full rounded-2xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm font-bold py-3 transition-all"
                            placeholder="Ex: Contexto histórico sobre o dom de línguas">
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                            <x-icon name="quote-left" style="solid" class="text-blue-500" />
                            Corpo da Exegese
                        </label>
                        <textarea name="content" id="content" rows="12" required
                            class="block w-full rounded-2xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all">{{ old('content') }}</textarea>
                    </div>
                </div>

                <!-- Media Section -->
                <div class="border-t border-slate-100 dark:border-slate-800 pt-8 mt-4">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                        <x-icon name="waveform-lines" style="solid" class="text-blue-500" />
                        Apoio em Áudio
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="audio_url" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Link Externo</label>
                            <input type="url" name="audio_url" id="audio_url" value="{{ old('audio_url') }}"
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 text-xs font-bold transition-all"
                                placeholder="Spotify, Podcast URL...">
                        </div>
                        <div>
                            <label for="audio" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Upload Direto</label>
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
                <span>Engrenagens</span>
                <x-icon name="gears" style="solid" />
            </h3>

            <div class="space-y-6">
                <div>
                    <label for="status" class="block text-xs font-bold text-slate-500 mb-2">Estado</label>
                    <select name="status" id="status" form="commentaryForm" required
                        class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 text-sm font-bold transition-all">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>Publicado</option>
                    </select>
                </div>

                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-950/30 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Comentário Oficial</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_official" value="1" form="commentaryForm" class="sr-only peer" {{ old('is_official') ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none dark:bg-slate-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" form="commentaryForm"
                    class="w-full py-4 bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white font-black rounded-2xl shadow-xl shadow-blue-500/10 transform hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                    <x-icon name="cloud-arrow-up" style="solid" />
                    SALVAR EXEGESE
                </button>
            </div>
        </div>

        <!-- Capa/Imagem Opcional -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 text-center">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center justify-between">
                <span>Ilustração</span>
                <x-icon name="image" style="solid" />
            </h3>

            <div class="relative group mx-auto w-full aspect-square rounded-[2rem] bg-slate-50 dark:bg-slate-950 border-2 border-dashed border-slate-200 dark:border-slate-800 flex flex-col items-center justify-center overflow-hidden transition-all hover:border-blue-500/50">
                <div id="cover-preview" class="absolute inset-0 z-0">
                     <x-icon name="microscope" style="solid" class="text-4xl text-slate-300 dark:text-slate-800 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" />
                </div>
                <div class="relative z-10 p-4">
                    <button type="button" onclick="document.getElementById('cover_image_file').click()"
                        class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white p-3 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 transform hover:scale-110 transition-all">
                        <x-icon name="camera" style="solid" />
                    </button>
                </div>
                <input type="file" name="cover_image_file" id="cover_image_file" form="commentaryForm" accept="image/*" class="hidden" onchange="previewCover(event)">
            </div>
            <p class="mt-4 text-[10px] text-slate-400 font-bold uppercase tracking-widest">Imagem de apoio (opcional)</p>
        </div>
    </div>
</div>
</div>
@endsection

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
