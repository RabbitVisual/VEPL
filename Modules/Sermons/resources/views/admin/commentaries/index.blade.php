@extends('admin::components.layouts.master')

@section('title', 'Comentários Bíblicos - Administração')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Comentários Bíblicos</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                <i class="fa-pro fa-solid fa-microscope text-blue-500"></i>
                Gestão de Exegese & Análise Versículo a Versículo
            </p>
        </div>
        <a href="{{ route('admin.sermons.commentaries.create') }}"
            class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-extrabold rounded-2xl shadow-lg shadow-blue-500/20 text-white bg-blue-600 hover:bg-blue-700 transform hover:-translate-y-0.5 transition-all">
            <i class="fa-pro fa-solid fa-plus-circle mr-2"></i>
            Novo Comentário
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
        <form method="GET" action="{{ route('admin.sermons.commentaries.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="relative">
                <i class="fa-pro fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full pl-9 pr-3 py-2.5 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all"
                    placeholder="Pesquisar no conteúdo...">
            </div>

            <div class="relative">
                <i class="fa-pro fa-solid fa-book-bible absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="book" value="{{ request('book') }}"
                    class="w-full pl-9 pr-3 py-2.5 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all font-bold"
                    placeholder="Filtrar por Livro (Ex: Gênesis)">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-slate-900 dark:bg-blue-600 text-white font-bold rounded-xl hover:bg-slate-800 dark:hover:bg-blue-700 transition-all shadow-md">
                    Filtrar
                </button>
                @if (request()->hasAny(['search', 'book']))
                    <a href="{{ route('admin.sermons.commentaries.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                        <i class="fa-pro fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Commentaries List -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50/50 dark:bg-slate-950/20">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Referência Exegética</th>
                        <th class="px-6 py-4 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Fragmento do Conteúdo</th>
                        <th class="px-6 py-4 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Autor</th>
                        <th class="px-6 py-4 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Gestão</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($commentaries as $comment)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 flex-shrink-0 relative group">
                                        @if($comment->cover_image)
                                            <img class="h-12 w-12 rounded-xl object-cover shadow-sm group-hover:scale-105 transition-transform" src="{{ asset('storage/' . $comment->cover_image) }}" alt="">
                                        @else
                                            <div class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center border border-slate-200 dark:border-slate-800 text-slate-400">
                                                <i class="fa-pro fa-solid fa-scroll text-lg"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $comment->reference }}</div>
                                        @if($comment->is_official)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[8px] font-black bg-blue-600 text-white uppercase tracking-tighter mt-1">
                                                OFICIAL
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 max-w-xs italic leading-relaxed">
                                    {{ Str::limit(strip_tags($comment->content), 80) }}
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}" class="h-7 w-7 rounded-full object-cover shadow-sm bg-slate-100 ring-2 ring-slate-50 dark:ring-slate-800">
                                    <span class="text-xs text-slate-700 dark:text-slate-300 font-bold">{{ $comment->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="px-3 py-1.5 text-[10px] font-extrabold rounded-xl border
                                    @if($comment->status === 'published')
                                        bg-green-50 text-green-700 border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-900/30
                                    @else
                                        bg-slate-50 text-slate-600 border-slate-100 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700
                                    @endif">
                                    <i class="fa-pro fa-solid fa-circle text-[6px] mr-1.5 opacity-50"></i>
                                    {{ $comment->status === 'published' ? 'Publicado' : 'Rascunho' }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center gap-3">
                                    <a href="{{ route('admin.sermons.commentaries.edit', $comment) }}"
                                        class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all" title="Editar">
                                        <i class="fa-pro fa-solid fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.sermons.commentaries.destroy', $comment) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Excluir este comentário bíblico permanentemente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all" title="Deletar">
                                            <i class="fa-pro fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20">
                                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                    <div class="w-20 h-20 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center mb-6">
                                        <i class="fa-pro fa-solid fa-microscope text-blue-500 text-3xl"></i>
                                    </div>
                                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-2">Sem Exegeses Registradas</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">Comece a documentar análises profundas de versículos específicos.</p>
                                    <a href="{{ route('admin.sermons.commentaries.create') }}"
                                        class="inline-flex items-center px-8 py-3.5 bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white text-sm font-extrabold rounded-2xl shadow-xl transition-all">
                                        <i class="fa-pro fa-solid fa-plus-circle mr-3"></i>
                                        Nova Análise
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-950/20">
            {{ $commentaries->links() }}
        </div>
    </div>
</div>
@endsection
