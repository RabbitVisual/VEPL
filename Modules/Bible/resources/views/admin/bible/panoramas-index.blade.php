@extends('admin::components.layouts.master')

@section('title', 'Panoramas dos Livros')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Panoramas dos Livros</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Gerencie autor, data, tema central e destinatários de cada livro.</p>
            </div>
            <a href="{{ route('admin.bible.panoramas.create') }}"
               class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                <x-icon name="plus" style="duotone" class="w-5 h-5 mr-2" />
                Novo panorama
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
            <form method="GET" class="flex flex-col sm:flex-row gap-3 sm:items-end">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Livro (número)</label>
                    <input type="number" min="1" max="66" name="book_number" value="{{ $qBook }}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white transition-colors">
                </div>
                <div class="w-full sm:w-64">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Testamento</label>
                    <select name="testament" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white transition-colors">
                        <option value="">Todos</option>
                        <option value="old" {{ $testament==='old'?'selected':'' }}>Antigo</option>
                        <option value="new" {{ $testament==='new'?'selected':'' }}>Novo</option>
                    </select>
                </div>
                <button type="submit"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-slate-900 hover:bg-slate-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                    Filtrar
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Lista</h2>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total: {{ $panoramas->total() }}</div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/30 text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3 text-left">Livro</th>
                            <th class="px-4 py-3 text-left">Testamento</th>
                            <th class="px-4 py-3 text-left">Autor</th>
                            <th class="px-4 py-3 text-left">Tema</th>
                            <th class="px-4 py-3 text-left">Idioma</th>
                            <th class="px-4 py-3 text-left">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($panoramas as $entry)
                            <tr>
                                <td class="px-4 py-3 font-mono text-blue-700 dark:text-blue-300">{{ $entry->book_number }}</td>
                                <td class="px-4 py-3">{{ $entry->testament === 'old' ? 'Antigo' : 'Novo' }}</td>
                                <td class="px-4 py-3">{{ $entry->author ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">
                                    {{ \Illuminate\Support\Str::limit((string) ($entry->theme_central ?? ''), 50) }}
                                </td>
                                <td class="px-4 py-3">{{ $entry->language ?? 'pt' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.bible.panoramas.edit', $entry->id) }}"
                                           class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-semibold bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                            Editar
                                        </a>
                                        <a href="{{ route('admin.bible.panoramas.show', $entry->id) }}"
                                           class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-semibold bg-white dark:bg-gray-900/20 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                                            Ver
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">
                                    Nenhum panorama encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $panoramas->links() }}
            </div>
        </div>
    </div>
@endsection

