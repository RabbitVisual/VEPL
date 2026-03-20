@extends('admin::components.layouts.master')

@section('title', 'Ver Panorama')

@section('content')
    <div class="p-6 space-y-6">
        @php($bibleRefs = app(\Modules\Bible\App\Services\BibleReferenceParserService::class))
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Panorama do Livro {{ $entry->book_number }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Testament: <span class="font-semibold">{{ $entry->testament }}</span> · Idioma: <span class="font-semibold">{{ $entry->language ?? 'pt' }}</span></p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.bible.panoramas.edit', $entry->id) }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-linear-to-r from-blue-600 to-blue-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                    <x-icon name="pen-to-square" style="duotone" class="w-4 h-4 mr-2" />
                    Editar
                </a>
                <a href="{{ route('admin.bible.panoramas.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                    <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" />
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 space-y-6">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Autor</p>
                    <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">{!! $bibleRefs->parseText(e((string) ($entry->author ?? '—'))) !!}</p>
                </div>

                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Data escrita</p>
                    <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">{!! $bibleRefs->parseText(e((string) ($entry->date_written ?? '—'))) !!}</p>
                </div>

                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Tema central</p>
                    <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">{!! $bibleRefs->parseText(e((string) ($entry->theme_central ?? '—'))) !!}</p>
                </div>

                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Destinatários</p>
                    <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">{!! $bibleRefs->parseText(e((string) ($entry->recipients ?? '—'))) !!}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

