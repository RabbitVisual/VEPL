@extends('admin::components.layouts.master')

@section('title', 'Criar Sermão - Administração')

@push('styles')
    @vite(['Modules/Sermons/resources/assets/sass/app.scss'])
@endpush

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Cofre de Sermões</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                    <i class="fa-pro fa-solid fa-microchip-ai text-blue-500"></i>
                    Homilética Digital & Exegese Profissional
                </p>
            </div>
            <a href="{{ route('admin.sermons.sermons.index') }}"
                class="inline-flex items-center px-4 py-2 border border-slate-200 dark:border-slate-800 text-sm font-bold rounded-xl text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                <i class="fa-pro fa-solid fa-arrow-left mr-2"></i>
                Voltar
            </a>
        </div>

        <!-- Form -->
        <form id="sermon-form" action="{{ route('admin.sermons.sermons.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">
                <div class="flex items-center justify-between mb-8 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                        <i class="fa-pro fa-solid fa-file-signature text-blue-600"></i>
                        Estrutura do Sermão
                    </h3>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full">Essencial</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Título -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Título do Sermão <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all"
                            placeholder="Ex: A Importância do Amor na Vida Cristã">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subtítulo -->
                    <div class="md:col-span-2">
                        <label for="subtitle" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Subtítulo / Tema Central
                        </label>
                        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}"
                            class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all"
                            placeholder="Frase de efeito ou tema teológico">
                    </div>

                    <!-- Category & Series -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:col-span-2">
                        <!-- Categoria -->
                        <div>
                            <label for="category_id" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Categoria
                            </label>
                            <select name="category_id" id="category_id"
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all">
                                <option value="">Selecione...</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Série -->
                        <div>
                            <label for="series_id" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Série Bíblica
                            </label>
                            <select name="series_id" id="series_id"
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all">
                                <option value="">Opcional</option>
                                @foreach ($series as $s)
                                    <option value="{{ $s->id }}" {{ old('series_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sugestão de Louvor -->
                        <div>
                            <label for="worship_suggestion_id" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Louvor Primário
                            </label>
                            <select name="worship_suggestion_id" id="worship_suggestion_id"
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all">
                                <option value="">Opcional</option>
                                @foreach ($worshipSongs as $song)
                                    <option value="{{ $song->id }}" {{ old('worship_suggestion_id') == $song->id ? 'selected' : '' }}>
                                        {{ $song->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Mídia & Links -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2 border-t border-slate-100 dark:border-slate-800 pt-6 mt-2">
                        <div>
                            <label for="audio_url" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-2">
                                <i class="fa-pro fa-solid fa-waveform-lines text-blue-500"></i>
                                URL de Áudio
                            </label>
                            <input type="url" name="audio_url" id="audio_url" value="{{ old('audio_url') }}"
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all"
                                placeholder="Spotify, SoundCloud ou MP3 Direto">
                        </div>
                        <div>
                            <label for="video_url" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-2">
                                <i class="fa-pro fa-solid fa-play-circle text-red-500"></i>
                                URL de Vídeo
                            </label>
                            <input type="url" name="video_url" id="video_url" value="{{ old('video_url') }}"
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all"
                                placeholder="YouTube, Vimeo ou Manuscrito Vídeo">
                        </div>
                    </div>

                    <!-- Audio/Cover/Attachments Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2">
                         <!-- Cover Image -->
                        <div>
                            <label for="cover_image_file" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Identidade Visual (Capa)
                            </label>
                            <div class="flex items-center space-x-4 p-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-slate-50/50 dark:bg-slate-950/20 transition-all hover:bg-slate-50 dark:hover:bg-slate-950/40">
                                <div id="cover-preview" class="w-16 h-16 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <i class="fa-pro fa-solid fa-image text-slate-300 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="cover_image_file" id="cover_image_file" accept="image/*"
                                        class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-extrabold file:bg-slate-900 file:text-white dark:file:bg-blue-600 hover:file:bg-blue-700 transition-all cursor-pointer"
                                        onchange="previewCover(event)">
                                    <p class="mt-1.5 text-[10px] text-slate-400 font-medium italic">Dimensão sugerida: 1200x630 (HD)</p>
                                </div>
                            </div>
                        </div>

                         <!-- Attachments -->
                        <div>
                            <label for="attachments" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Documentos e Recursos
                            </label>
                            <input type="file" name="attachments[]" id="attachments" multiple accept=".pdf,.doc,.docx,.txt"
                                class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/20 dark:file:text-blue-400 transition-all">
                            <p class="mt-1.5 text-[10px] text-slate-400">PDF, PPTX ou Manuscritos (Máx 10MB/cada)</p>
                        </div>
                    </div>

                    <!-- Meta -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2 border-t border-slate-100 dark:border-slate-800 pt-6 mt-2">
                        <!-- Data -->
                        <div>
                            <label for="sermon_date" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-2">
                                <i class="fa-pro fa-solid fa-calendar-day text-blue-500"></i>
                                Data de Ministração
                            </label>
                            <input type="date" name="sermon_date" id="sermon_date" value="{{ old('sermon_date', now()->format('Y-m-d')) }}"
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all">
                        </div>

                        <!-- Status/Visibility -->
                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-2">
                                <i class="fa-pro fa-solid fa-globe text-blue-500"></i>
                                Publicação & Visibilidade
                            </label>
                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <select name="status" id="status" class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all">
                                        <option value="draft" selected>Rascunho</option>
                                        <option value="published">Publicado</option>
                                        <option value="archived">Arquivado</option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <select name="visibility" id="visibility" class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all">
                                        <option value="public">Público</option>
                                        <option value="members">Membros</option>
                                        <option value="private" selected>Privado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
     <!-- Tipo de estrutura homilética -->
                        <div class="md:col-span-2">
                            <label for="sermon_structure_type" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-2">
                                <i class="fa-pro fa-solid fa-scroll text-slate-400"></i>
                                Método Homilético
                            </label>
                            <select name="sermon_structure_type" id="sermon_structure_type"
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all">
                                <option value="">Nenhum (Livre)</option>
                                <option value="expositivo" {{ old('sermon_structure_type') === 'expositivo' ? 'selected' : '' }}>Sermão Expositivo</option>
                                <option value="temático" {{ old('sermon_structure_type') === 'temático' ? 'selected' : '' }}>Sermão Temático</option>
                                <option value="textual" {{ old('sermon_structure_type') === 'textual' ? 'selected' : '' }}>Sermão Textual</option>
                            </select>
                            <div class="mt-4 p-5 rounded-2xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/30">
                                <p class="text-[10px] font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                    <i class="fa-pro fa-solid fa-lightbulb"></i> Guia Rápido de Isaltino Coelho
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                                    <div class="space-y-1">
                                        <p class="font-bold text-slate-900 dark:text-white">Expositivo</p>
                                        <p class="text-slate-500 dark:text-slate-400">Contexto e aplicação do texto sagrado.</p>
                                    </div>
                                    <div class="space-y-1 border-l sm:pl-4 border-slate-200 dark:border-slate-800">
                                        <p class="font-bold text-slate-900 dark:text-white">Temático</p>
                                        <p class="text-slate-500 dark:text-slate-400">Doutrina e tópicos teológicos.</p>
                                    </div>
                                    <div class="space-y-1 border-l sm:pl-4 border-slate-200 dark:border-slate-800">
                                        <p class="font-bold text-slate-900 dark:text-white">Textual</p>
                                        <p class="text-slate-500 dark:text-slate-400">Divisões extraídas do próprio versículo.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tags -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                    <i class="fa-pro fa-solid fa-tags text-blue-600"></i>
                    Palavras-Chave (Tags)
                </h3>
                <div class="flex items-center gap-3 mb-6">
                    <div class="relative flex-1">
                        <i class="fa-pro fa-solid fa-plus-circle absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="tag-input"
                            class="w-full pl-10 rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 shadow-sm focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all"
                            placeholder="Ex: Gracia, Salvação, Família...">
                    </div>
                    <button type="button" id="add-tag"
                        class="px-6 py-2.5 bg-slate-900 dark:bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-slate-800 dark:hover:bg-blue-700 transition-all shadow-md">
                        Inserir
                    </button>
                </div>
                <div id="selected-tags" class="flex flex-wrap gap-2.5">
                    <!-- JS will populate -->
                </div>
            </div>

            <!-- Sermon Content -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">
                <div class="flex items-center justify-between mb-6 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                        <i class="fa-pro fa-solid fa-pen-nib text-blue-600"></i>
                        Manuscrito / Esboço
                    </h3>
                    <div class="flex items-center gap-2 px-3 py-1 bg-blue-50 dark:bg-blue-900/20 rounded-full">
                        <i class="fa-pro fa-solid fa-sparkles text-blue-500 text-[10px]"></i>
                        <span class="text-[10px] font-bold text-blue-700 dark:text-blue-400 uppercase tracking-widest">Elias Studio Ativo</span>
                    </div>
                </div>

                <div class="space-y-6">
                    <x-rich-editor name="full_content" value="{{ old('full_content') }}" />
                </div>
                <div class="mt-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800 flex items-start gap-3">
                    <i class="fa-pro fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                        <strong>Dica Teológica:</strong> Use <kbd class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono">@</kbd> seguido do livro (ex: @Gálatas 2:20) para que o Elias linke automaticamente as referências bíblicas ao seu texto.
                    </p>
                </div>

                <!-- Legacy Fields Collapsed -->
                <details class="mt-8 border-t border-slate-100 dark:border-slate-800 pt-6 group">
                    <summary class="cursor-pointer text-xs font-extrabold text-slate-400 hover:text-blue-600 uppercase tracking-widest flex items-center gap-2 transition-colors">
                        <i class="fa-pro fa-solid fa-layer-group group-open:rotate-180 transition-transform"></i>
                        Estrutura Tradicional (Opcional)
                    </summary>
                    <div class="grid gap-6 mt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Introdução</label>
                                <textarea name="introduction" rows="3" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-sm focus:ring-blue-500/20 focus:border-blue-500">{{ old('introduction') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Conclusão / Apelo</label>
                                <textarea name="conclusion" rows="3" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-sm focus:ring-blue-500/20 focus:border-blue-500">{{ old('conclusion') }}</textarea>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Esqueleto / Tópicos</label>
                            <textarea name="development" rows="4" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-sm focus:ring-blue-500/20 focus:border-blue-500" placeholder="I. Tópico...&#10;II. Tópico..."></textarea>
                        </div>
                    </div>
                </details>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pb-20 mt-8">
                <a href="{{ route('admin.sermons.sermons.index') }}"
                    class="px-8 py-3.5 border border-slate-200 dark:border-slate-800 text-sm font-bold rounded-2xl text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                    Descartar
                </a>
                <button type="submit"
                    class="px-10 py-3.5 border border-transparent text-sm font-extrabold rounded-2xl text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transform hover:-translate-y-0.5 transition-all">
                    Publicar no Cofre
                </button>
            </div>
        </form>
        </div>

        <!-- Sidebar: Contexto Bíblico + Elias -->
        <aside class="space-y-4 order-first lg:order-last">
            @include('sermons::admin.sermons.partials.contexto-biblico', ['bibleBooks' => $bibleBooks])
            @include('sermons::admin.sermons.partials.elias-sermon-studio')
        </aside>
    </div>

    @include('sermons::admin.sermons.partials.bible-picker')

    <!-- Tags JS Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sermonForm = document.getElementById('sermon-form');
            if (sermonForm) sermonForm.addEventListener('submit', function() {
                window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando...' } }));
            });
            const tagInput = document.getElementById('tag-input');
            const addTagBtn = document.getElementById('add-tag');
            const tagsContainer = document.getElementById('selected-tags');

            function addTag(name) {
                if(!name) return;
                const tag = document.createElement('span');
                tag.className = 'inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-100 dark:border-blue-800/50 shadow-sm transition-all animate-in fade-in zoom-in duration-200';
                tag.innerHTML = `
                    <i class="fa-pro fa-solid fa-hashtag mr-2 text-[10px] opacity-50"></i>
                    ${name}
                    <button type="button" class="ml-2 text-blue-400 hover:text-blue-600 transition-colors" onclick="this.parentElement.remove()">
                        <i class="fa-pro fa-solid fa-xmark"></i>
                    </button>
                    <input type="hidden" name="tags[]" value="${name}">
                `;
                tagsContainer.appendChild(tag);
                tagInput.value = '';
            }

            addTagBtn.addEventListener('click', () => addTag(tagInput.value));
            tagInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addTag(tagInput.value);
                }
            });
        });

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
@endsection
