@extends('admin::components.layouts.master')

@section('title', 'Comentários Oficiais')

@section('content')
    <div class="min-h-screen bg-slate-50 p-6">
        <div class="mx-auto max-w-7xl space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Comentários Oficiais</h1>
                    <p class="mt-1 text-sm text-slate-600">Gestão editorial do comentário teológico oficial por versículo.</p>
                </div>
                <a href="{{ route('admin.bible.metadata.create') }}" class="inline-flex items-center rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700">
                    <x-icon name="plus" class="mr-2 h-4 w-4" />
                    Novo comentário
                </a>
            </div>

            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <form method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-6">
                    <select name="bible_version_id" class="rounded-xl border-slate-300 text-sm text-slate-900 focus:border-amber-600 focus:ring-amber-600">
                        <option value="">Versão</option>
                        @foreach($versions as $version)
                            <option value="{{ $version->id }}" @selected($versionId === $version->id)>{{ $version->abbreviation }}</option>
                        @endforeach
                    </select>

                    <select name="book_id" class="rounded-xl border-slate-300 text-sm text-slate-900 focus:border-amber-600 focus:ring-amber-600">
                        <option value="">Livro</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}" @selected($bookId === $book->id)>{{ $book->book_number }} - {{ $book->name }}</option>
                        @endforeach
                    </select>

                    <select name="chapter_id" class="rounded-xl border-slate-300 text-sm text-slate-900 focus:border-amber-600 focus:ring-amber-600">
                        <option value="">Capítulo</option>
                        @foreach($chapters as $chapter)
                            <option value="{{ $chapter->id }}" @selected($chapterId === $chapter->id)>{{ $chapter->chapter_number }}</option>
                        @endforeach
                    </select>

                    <select name="verse_id" class="rounded-xl border-slate-300 text-sm text-slate-900 focus:border-amber-600 focus:ring-amber-600">
                        <option value="">Versículo</option>
                        @foreach($verses as $verse)
                            <option value="{{ $verse->id }}" @selected($verseId === $verse->id)>{{ $verse->verse_number }}</option>
                        @endforeach
                    </select>

                    <select name="status" class="rounded-xl border-slate-300 text-sm text-slate-900 focus:border-amber-600 focus:ring-amber-600">
                        <option value="">Status</option>
                        <option value="published" @selected($status === 'published')>Publicado</option>
                        <option value="draft" @selected($status === 'draft')>Rascunho</option>
                    </select>

                    <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Filtrar</button>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-100 text-slate-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Referência</th>
                                <th class="px-4 py-3 text-left">Comentário</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Atualizado</th>
                                <th class="px-4 py-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($items as $item)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-800">
                                        {{ $item->book?->bibleVersion?->abbreviation }} - {{ $item->book?->name }} {{ $item->chapter?->chapter_number }}:{{ $item->verse?->verse_number }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{!! \Illuminate\Support\Str::limit(strip_tags((string) $item->official_commentary), 140) !!}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $item->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $item->is_published ? 'Publicado' : 'Rascunho' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $item->updated_at?->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.bible.metadata.edit', $item) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                            Editar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">Nenhum comentário oficial encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 p-4">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
