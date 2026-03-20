@extends('admin::components.layouts.master')

@section('title', 'Word Tags (Interlinear)')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Word Tags (Interlinear) — `bible_word_tags`</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">CRUD permitido com paginação e filtros. Use com cuidado: volume de dados pode ser grande.</p>
            </div>
            <a href="{{ route('admin.bible.word-tags.create') }}"
               class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                <x-icon name="plus" style="duotone" class="w-5 h-5 mr-2" />
                Novo
            </a>
        </div>

        @if (session('success'))
            <div class="p-4 text-sm bg-green-50 border border-green-200 text-green-900 rounded-lg dark:bg-green-900/20 dark:border-green-800 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif
        @if (session('info'))
            <div class="p-4 text-sm bg-blue-50 border border-blue-200 text-blue-900 rounded-lg dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-200">
                {{ session('info') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Verse ID</label>
                    <input type="number" name="verse_id" value="{{ $verseId }}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white transition-colors">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Strong number</label>
                    <input type="text" name="strong_number" value="{{ $strong }}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white transition-colors" placeholder="Ex: G2316">
                </div>
                <div class="w-full lg:w-48">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Idioma</label>
                    <select name="lang" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white transition-colors">
                        <option value="">Todos</option>
                        <option value="he" {{ $lang==='he'?'selected':'' }}>he</option>
                        <option value="gr" {{ $lang==='gr'?'selected':'' }}>gr</option>
                    </select>
                </div>
                <button type="submit"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                    Filtrar
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Lista</h2>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total: {{ $wordTags->total() }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/30 text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Verse</th>
                            <th class="px-4 py-3 text-left">Pos</th>
                            <th class="px-4 py-3 text-left">Surface</th>
                            <th class="px-4 py-3 text-left">Strong</th>
                            <th class="px-4 py-3 text-left">Morph</th>
                            <th class="px-4 py-3 text-left">Lang</th>
                            <th class="px-4 py-3 text-left">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($wordTags as $entry)
                            <tr>
                                <td class="px-4 py-3 font-mono text-blue-700 dark:text-blue-300">{{ $entry->id }}</td>
                                <td class="px-4 py-3 font-mono">{{ $entry->verse_id }}</td>
                                <td class="px-4 py-3 font-mono">{{ $entry->position }}</td>
                                <td class="px-4 py-3">{{ $entry->word_surface }}</td>
                                <td class="px-4 py-3 font-mono">{{ $entry->strong_number ?? '—' }}</td>
                                <td class="px-4 py-3 font-mono">{{ $entry->morphology ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $entry->lang }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.bible.word-tags.edit', $entry->id) }}"
                                           class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-semibold bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                            Editar
                                        </a>
                                        <a href="{{ route('admin.bible.word-tags.show', $entry->id) }}"
                                           class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-semibold bg-white dark:bg-gray-900/20 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                                            Ver
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">
                                    Nenhum registro encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $wordTags->links() }}
            </div>
        </div>
    </div>
@endsection

