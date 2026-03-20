@extends('admin::components.layouts.master')

@section('title', 'Novo Estudo - Administração')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
<!-- Page Header -->
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Novo Estudo Teológico</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
            <x-icon name="graduation-cap" style="solid" class="text-blue-500" />
            Materiais de Discipulado & Acadêmicos
        </p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.sermons.studies.index') }}"
            class="inline-flex items-center px-4 py-2 border border-slate-200 dark:border-slate-800 text-sm font-bold rounded-xl text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm">
            <x-icon name="arrow-left" style="solid" class="mr-2" />
            Voltar
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-sm border border-slate-200 dark:border-slate-800 p-8">
            <form action="{{ route('admin.sermons.studies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="studyForm">
                @csrf

                <!-- Basic Info -->
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
                            <x-icon name="heading" style="solid" class="text-blue-500" />
                            Título do Estudo
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="block w-full rounded-2xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm font-bold py-3 transition-all"
                            placeholder="Ex: A Doutrina da Justificação">
                    </div>

                    <div>
                        <label for="subtitle" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
                            <x-icon name="subscript" style="solid" class="text-slate-400 text-xs" />
                            Subtítulo ou Tema Central
                        </label>
                        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}"
                            class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all"
                            placeholder="Uma análise reformada sobre Romanos 5">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="sermon_series_id" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
                                <x-icon name="layer-group" style="solid" class="text-slate-400" />
                                Série Teológica
                            </label>
                            <select name="sermon_series_id" id="sermon_series_id"
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all font-bold">
                                <option value="">Estudo Avulso</option>
                                @foreach($series as $s)
                                    <option value="{{ $s->id }}" {{ old('sermon_series_id') == $s->id ? 'selected' : '' }}>{{ $s->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
                                <x-icon name="tag" style="solid" class="text-slate-400" />
                                Categoria Acadêmica
                            </label>
                            <select name="category_id" id="category_id" required
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all font-bold">
                                <option value="">Selecione...</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Media Section -->
                <div class="border-t border-slate-100 dark:border-slate-800 pt-8 mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                            <x-icon name="play-circle" style="solid" class="text-blue-500" />
                            Anexos Multinível
                        </h3>
                    </div>

                    <div>
                        <label for="video_url" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
                            <x-icon name="youtube" style="brands" class="text-red-500" />
                            Link do Vídeo
                        </label>
                        <input type="url" name="video_url" id="video_url" value="{{ old('video_url') }}"
                            class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all"
                            placeholder="YouTube, Vimeo ou Manuscrito Vídeo">
                    </div>

                    <div>
                        <label for="audio_url" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
                            <x-icon name="waveform-lines" style="solid" class="text-blue-500" />
                            Link do Áudio/Podcast
                        </label>
                        <input type="url" name="audio_url" id="audio_url" value="{{ old('audio_url') }}"
                            class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all"
                            placeholder="Spotify, SoundCloud ou SoundCloud MP3">
                    </div>

                    <div class="md:col-span-2 bg-slate-50 dark:bg-slate-950/30 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                        <label for="audio_file" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Upload de Áudio Nativo (MP3/M4A)</label>
                        <input type="file" name="audio_file" id="audio_file" accept="audio/*"
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-slate-900 file:text-white hover:file:bg-slate-800 transition-all cursor-pointer">
                    </div>
                </div>

                <!-- Content -->
                <div class="border-t border-slate-100 dark:border-slate-800 pt-8 mt-4">
                    <label for="content" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                        <x-icon name="feather-pointed" style="solid" class="text-blue-500" />
                        Plexo do Estudo (Conteúdo Profundo)
                    </label>
                    <textarea name="content" id="content" rows="15" required
                        class="block w-full rounded-2xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all">{{ old('content') }}</textarea>
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
                    <label for="status" class="block text-xs font-bold text-slate-500 mb-2">Estado Ministerial</label>
                    <select name="status" id="status" form="studyForm" required
                        class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 text-sm font-bold transition-all">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Arquivado</option>
                    </select>
                </div>

                <div>
                    <label for="visibility" class="block text-xs font-bold text-slate-500 mb-2">Privacidade</label>
                    <select name="visibility" id="visibility" form="studyForm" required
                        class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 text-sm font-bold transition-all">
                        <option value="public" {{ old('visibility', 'members') == 'public' ? 'selected' : '' }}>Geral / Público</option>
                        <option value="members" {{ old('visibility', 'members') == 'members' ? 'selected' : '' }}>Somente Membros</option>
                        <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>Somente Administradores</option>
                    </select>
                </div>

                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-950/30 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Estudo em Destaque</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" form="studyForm" class="sr-only peer" {{ old('is_featured') ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none dark:bg-slate-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" form="studyForm"
                    class="w-full py-4 bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white font-black rounded-2xl shadow-xl shadow-blue-500/10 transform hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                    <x-icon name="cloud-arrow-up" style="solid" />
                    PUBLICAR ESTUDO
                </button>
            </div>
        </div>

        <!-- Capa -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 text-center">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center justify-between">
                <span>Visual Acadêmico</span>
                <x-icon name="image" style="solid" />
            </h3>

            <div class="relative group mx-auto w-full aspect-video rounded-[2rem] bg-slate-50 dark:bg-slate-950 border-2 border-dashed border-slate-200 dark:border-slate-800 flex flex-col items-center justify-center overflow-hidden transition-all hover:border-blue-500/50">
                <div id="cover-preview" class="absolute inset-0 z-0">
                     <x-icon name="scroll-old" style="solid" class="text-5xl text-slate-300 dark:text-slate-800 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" />
                </div>
                <div class="relative z-10 p-4">
                    <button type="button" onclick="document.getElementById('cover_image_file').click()"
                        class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white p-3 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 transform hover:scale-110 transition-all">
                        <x-icon name="camera" style="solid" />
                    </button>
                </div>
                <input type="file" name="cover_image_file" id="cover_image_file" form="studyForm" accept="image/*" class="hidden" onchange="previewCover(event)">
            </div>
            <p class="mt-4 text-[10px] text-slate-400 font-bold uppercase tracking-widest">Capas Estilizadas recomendadas</p>
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
@endpush
@endsection
