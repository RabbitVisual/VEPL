@extends('admin::components.layouts.master')

@section('title', 'Ver Tag Interlinear #'.$entry->id)

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Tag Interlinear #{{ $entry->id }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Versículo: <span class="font-mono">{{ $entry->verse_id }}</span> · Posição: <span class="font-mono">{{ $entry->position }}</span></p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.bible.word-tags.edit', $entry->id) }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-amber-600 rounded-lg shadow-sm hover:bg-amber-700 transition-all duration-200">
                    <x-icon name="pen-to-square" style="duotone" class="w-4 h-4 mr-2" />
                    Editar
                </a>
                <a href="{{ route('admin.bible.word-tags.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                    <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" />
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 space-y-5">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Palavra Original</p>
                    <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">{{ $entry->word_surface }}</p>
                </div>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Strong</p>
                        <p class="text-sm text-gray-700 dark:text-gray-200 font-mono">{{ $entry->strong_number ?? '—' }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Idioma</p>
                        <p class="text-sm text-gray-700 dark:text-gray-200">{{ $entry->lang === 'he' ? 'Hebraico' : 'Grego' }}</p>
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Morfologia</p>
                    <p class="text-sm text-gray-700 dark:text-gray-200 font-mono">{{ $entry->morphology ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

