@extends('admin::components.layouts.master')

@section('title', 'Editar Sermão - Administração')

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
                    <i class="fa-pro fa-solid fa-pen-to-square text-blue-500"></i>
                    Editando Esboço Homilético
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.sermons.sermons.show', $sermon) }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-slate-200 dark:border-slate-800 text-sm font-bold rounded-xl text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                    <i class="fa-pro fa-solid fa-eye mr-2 text-blue-500"></i>
                    Ver
                </a>
                <a href="{{ route('admin.sermons.sermons.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-slate-200 dark:border-slate-800 text-sm font-bold rounded-xl text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                    <i class="fa-pro fa-solid fa-arrow-left mr-2"></i>
                    Voltar
                </a>
            </div>
        </div>

        <!-- Form -->
        <form id="sermon-form" action="{{ route('admin.sermons.sermons.update', $sermon) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">
                <div class="flex items-center justify-between mb-8 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                        <i class="fa-pro fa-solid fa-file-signature text-blue-600"></i>
                        Estrutura do Sermão
                    </h3>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full">ID: #{{ $sermon->id }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Título -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Título do Sermão <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title', $sermon->title) }}" required
                            class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subtítulo -->
                    <div class="md:col-span-2">
                        <label for="subtitle" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Subtítulo / Tema Central
                        </label>
                        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $sermon->subtitle) }}"
                            class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all">
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
                                    <option value="{{ $category->id }}" {{ old('category_id', $sermon->category_id) == $category->id ? 'selected' : '' }}>
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
                                    <option value="{{ $s->id }}" {{ old('series_id', $sermon->series_id) == $s->id ? 'selected' : '' }}>
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
                                    <option value="{{ $song->id }}" {{ old('worship_suggestion_id', $sermon->worship_suggestion_id) == $song->id ? 'selected' : '' }}>
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
                            <input type="url" name="audio_url" id="audio_url" value="{{ old('audio_url', $sermon->audio_url) }}"
                                class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all"
                                placeholder="Spotify, SoundCloud ou link MP3">
                        </div>
                        <div>
                            <label for="video_url" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-2">
                                <i class="fa-pro fa-solid fa-play-circle text-red-500"></i>
                                URL de Vídeo
                            </label>
                            <input type="url" name="video_url" id="video_url" value="{{ old('video_url', $sermon->video_url) }}"
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
                                    @if($sermon->cover_image)
                                        <img src="{{ asset('storage/' . $sermon->cover_image) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-pro fa-solid fa-image text-slate-300 text-xl"></i>
                                    @endif
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

                            @if(!empty($sermon->attachments))
                                <div class="mt-3 space-y-2">
                                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Arquivos Atuais:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($sermon->attachments as $file)
                                            <div class="flex items-center gap-2 px-2.5 py-1.5 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-lg shadow-sm">
                                                <i class="fa-pro fa-solid fa-file-pdf text-red-500 text-xs"></i>
                                                <a href="{{ Storage::url($file['path'] ?? '') }}" target="_blank" class="text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-blue-600">{{ $file['name'] ?? 'Arquivo' }}</a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
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
                            <input type="date" name="sermon_date" id="sermon_date" value="{{ old('sermon_date', $sermon->sermon_date ? $sermon->sermon_date->format('Y-m-d') : '') }}"
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
                                        <option value="draft" {{ $sermon->status === 'draft' ? 'selected' : '' }}>Rascunho</option>
                                        <option value="published" {{ $sermon->status === 'published' ? 'selected' : '' }}>Publicado</option>
                                        <option value="archived" {{ $sermon->status === 'archived' ? 'selected' : '' }}>Arquivado</option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <select name="visibility" id="visibility" class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all">
                                        <option value="public" {{ $sermon->visibility === 'public' ? 'selected' : '' }}>Público</option>
                                        <option value="members" {{ $sermon->visibility === 'members' ? 'selected' : '' }}>Membros</option>
                                        <option value="private" {{ $sermon->visibility === 'private' ? 'selected' : '' }}>Privado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo de estrutura homilética -->
                    <div class="md:col-span-2 border-t border-slate-100 dark:border-slate-800 pt-6 mt-2">
                        <label for="sermon_structure_type" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center gap-2">
                            <i class="fa-pro fa-solid fa-scroll text-slate-400"></i>
                            Método Homilético
                        </label>
                        <select name="sermon_structure_type" id="sermon_structure_type"
                            class="block w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all">
                            <option value="">Nenhum (Livre)</option>
                            <option value="expositivo" {{ old('sermon_structure_type', $sermon->sermon_structure_type) === 'expositivo' ? 'selected' : '' }}>Sermão Expositivo</option>
                            <option value="temático" {{ old('sermon_structure_type', $sermon->sermon_structure_type) === 'temático' ? 'selected' : '' }}>Sermão Temático</option>
                            <option value="textual" {{ old('sermon_structure_type', $sermon->sermon_structure_type) === 'textual' ? 'selected' : '' }}>Sermão Textual</option>
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

            <!-- Tags -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white mb-4 flex items-center gap-3">
                    <i class="fa-pro fa-solid fa-tags text-blue-600"></i>
                    Tags & Palavras-chave
                </h3>
                <div class="flex items-center gap-2 mb-4">
                    <input type="text" id="tag-input"
                        class="flex-1 rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:border-blue-500 focus:ring-blue-500/20 sm:text-sm transition-all"
                        placeholder="Adicione tags (ex: 'graça', 'fé', 'amor')">
                    <button type="button" id="add-tag"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                        <i class="fa-pro fa-solid fa-plus mr-2"></i>
                        Adicionar
                    </button>
                </div>
                <div id="selected-tags" class="flex flex-wrap gap-2">
                    {{-- Tags will be loaded here by JS --}}
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
                    <x-rich-editor name="full_content" value="{!! old('full_content', $sermon->full_content) !!}" />
                </div>
                <div class="mt-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800 flex items-start gap-3">
                    <i class="fa-pro fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                        <strong>Dica Teológica:</strong> Use <kbd class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono">@</kbd> seguido do livro para linkar referências bíblicas automaticamente.
                    </p>
                </div>

                <!-- Legacy Fields Collapsed -->
                <details class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <summary class="cursor-pointer text-sm font-medium text-gray-500 hover:text-amber-500">Mostrar Estrutura Tradicional (Opcional)</summary>
                    <div class="grid gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Introdução</label>
                            <textarea name="introduction" rows="3" class="w-full rounded-md border-gray-300 dark:bg-gray-700">{{ old('introduction', $sermon->introduction) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Desenvolvimento</label>
                            <textarea name="development" rows="3" class="w-full rounded-md border-gray-300 dark:bg-gray-700">{{ old('development', $sermon->development) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Conclusão</label>
                            <textarea name="conclusion" rows="3" class="w-full rounded-md border-gray-300 dark:bg-gray-700">{{ old('conclusion', $sermon->conclusion) }}</textarea>
                        </div>
                    </div>
                </details>
            </div>

            <!-- Co-autores -->
            @if($sermon->user_id === auth()->id())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-icon name="user-group" class="w-5 h-5 text-amber-500" />
                    Co-autores
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-xs font-bold cursor-help" title="Convide outros para editar este sermão; apenas você pode remover colaboradores." aria-label="Ajuda">?</span>
                </h3>
                <ul class="space-y-3 mb-4">
                    @forelse($sermon->collaborators as $collab)
                        @php
                            $name = $collab->user->name ?? $collab->user->email;
                            $initial = mb_strtoupper(mb_substr($name, 0, 1));
                            $color = $collab->status === 'accepted' ? 'bg-green-500' : ($collab->status === 'pending' ? 'bg-amber-500' : 'bg-gray-500');
                        @endphp
                        <li class="flex items-center justify-between gap-3 text-sm">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-9 h-9 rounded-full {{ $color }} text-white flex items-center justify-center text-sm font-bold shrink-0" aria-hidden="true">{{ $initial }}</span>
                                <span class="text-gray-900 dark:text-white truncate">{{ $name }}</span>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium shrink-0 {{ $collab->status === 'accepted' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' : ($collab->status === 'pending' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400') }}">
                                @if($collab->status === 'accepted')<x-icon name="check" class="w-3 h-3" />@elseif($collab->status === 'pending')<x-icon name="clock" class="w-3 h-3" />@else<x-icon name="xmark" class="w-3 h-3" />@endif
                                {{ $collab->status_display }}
                            </span>
                        </li>
                    @empty
                        <li class="flex flex-col items-center justify-center py-6 text-center rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-dashed border-gray-200 dark:border-gray-700">
                            <div class="w-12 h-12 rounded-full bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center mx-auto mb-3">
                                <x-icon name="user-group" class="w-6 h-6 text-amber-500" />
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nenhum co-autor ainda.</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Convide alguém abaixo para editar este sermão com você.</p>
                        </li>
                    @endforelse
                </ul>
                <form method="post" action="{{ route('admin.sermons.sermons.collaborators.invite', $sermon) }}" class="flex gap-2">
                    @csrf
                    <input type="email" name="email" placeholder="E-mail do colaborador" required
                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700">Convidar</button>
                </form>
                @if(session('error'))<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ session('error') }}</p>@endif
                @if(session('success'))<p class="mt-2 text-sm text-green-600 dark:text-green-400">{{ session('success') }}</p>@endif
            </div>
            @endif

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pb-20 mt-8">
                <a href="{{ route('admin.sermons.sermons.index') }}"
                    class="px-8 py-3.5 border border-slate-200 dark:border-slate-800 text-sm font-bold rounded-2xl text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                    Descartar Alteraçções
                </a>
                <button type="submit"
                    class="px-10 py-3.5 border border-transparent text-sm font-extrabold rounded-2xl text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transform hover:-translate-y-0.5 transition-all">
                    <i class="fa-pro fa-solid fa-cloud-arrow-up mr-2"></i>
                    Atualizar no Cofre
                </button>
            </div>
        </form>
        </div>

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

            // Load existing tags
            @if($sermon->tags)
                @foreach($sermon->tags as $tag)
                    addTag("{{ $tag->name }}");
                @endforeach
            @endif

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
