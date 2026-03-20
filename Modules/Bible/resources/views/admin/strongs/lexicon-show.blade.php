@extends('admin::components.layouts.master')

@section('title', 'Ver Strong '.$entry->number)

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Termo Strong {{ $entry->number }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Idioma: <span class="font-semibold">{{ $entry->lang }}</span></p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.bible.strongs-lexicon.edit', $entry->id) }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-amber-600 rounded-lg shadow-sm hover:bg-amber-700 transition-all duration-200">
                    <x-icon name="pen-to-square" style="duotone" class="w-4 h-4 mr-2" />
                    Editar
                </a>
                <a href="{{ route('admin.bible.strongs-lexicon.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                    <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" />
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 bg-linear-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Detalhes do Léxico</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Lemma</p>
                        <p class="text-sm text-gray-700 dark:text-gray-200">{{ $entry->lemma }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Pronúncia</p>
                        <p class="text-sm text-gray-700 dark:text-gray-200">{{ $entry->pronounce }}</p>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">XLit</p>
                    <p class="text-sm text-gray-700 dark:text-gray-200">{{ $entry->xlit }}</p>
                </div>

                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Equivalente Semântico (PT)</p>
                    <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">{{ $entry->lemma_br }}</p>
                </div>

                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Descrição PT</p>
                    <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">{{ $entry->description_pt }}</p>
                </div>

                <div class="flex flex-wrap gap-3 items-center">
                    @if($entry->is_reviewed)
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                            Revisado
                        </span>
                    @else
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 dark:bg-gray-900/30 dark:text-gray-300">
                            Não revisado
                        </span>
                    @endif

                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        Revisado em: {{ $entry->reviewed_at?->format('d M, Y H:i') ?? '—' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection

