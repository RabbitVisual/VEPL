@extends('admin::components.layouts.master')

@section('title', 'Sermões - Administração')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Cofre de Sermões</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                <i class="fa-pro fa-solid fa-vault text-blue-500"></i>
                Gestão de Conteúdo Homilético & Ministerial
            </p>
        </div>
        <a href="{{ route('admin.sermons.sermons.create') }}"
            class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-extrabold rounded-2xl shadow-lg shadow-blue-500/20 text-white bg-blue-600 hover:bg-blue-700 transform hover:-translate-y-0.5 transition-all">
            <i class="fa-pro fa-solid fa-plus-circle mr-2"></i>
            Novo Esboço
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
        <form method="GET" action="{{ route('admin.sermons.sermons.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="relative">
                <i class="fa-pro fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full pl-9 pr-3 py-2.5 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all"
                    placeholder="Pesquisar...">
            </div>
            <select name="category_id" class="px-3 py-2.5 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all">
                <option value="">Categorias</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-2.5 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all">
                <option value="">Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arquivado</option>
            </select>
            <select name="visibility" class="px-3 py-2.5 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all">
                <option value="">Visibilidade</option>
                <option value="public" {{ request('visibility') === 'public' ? 'selected' : '' }}>Público</option>
                <option value="members" {{ request('visibility') === 'members' ? 'selected' : '' }}>Membros</option>
                <option value="private" {{ request('visibility') === 'private' ? 'selected' : '' }}>Privado</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-slate-900 dark:bg-blue-600 text-white font-bold rounded-xl hover:bg-slate-800 dark:hover:bg-blue-700 transition-all shadow-md">
                    Filtrar
                </button>
                @if (request()->hasAny(['search', 'category_id', 'status', 'visibility']))
                    <a href="{{ route('admin.sermons.sermons.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                        <i class="fa-pro fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Sermons List -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50/50 dark:bg-slate-950/20">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Sermão / Manuscrito</th>
                        <th class="px-6 py-4 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Temática</th>
                        <th class="px-6 py-4 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Autor</th>
                        <th class="px-6 py-4 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Alcance</th>
                        <th class="px-6 py-4 text-right text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Gestão</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($sermons as $sermon)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 flex-shrink-0 relative group">
                                        @if($sermon->cover_image)
                                            <img class="h-12 w-12 rounded-xl object-cover shadow-sm group-hover:scale-105 transition-transform" src="{{ asset('storage/' . $sermon->cover_image) }}" alt="">
                                        @else
                                            <div class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center border border-slate-200 dark:border-slate-800">
                                                <i class="fa-pro fa-solid fa-book-open text-slate-400 text-lg"></i>
                                            </div>
                                        @endif
                                        @if($sermon->audio_url)
                                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center text-[8px] text-white shadow-sm border border-white dark:border-slate-900">
                                                <i class="fa-pro fa-solid fa-microphone"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $sermon->title }}</div>
                                        <div class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center gap-2 mt-0.5">
                                            <span class="font-bold flex items-center gap-1">
                                                <i class="fa-pro fa-solid fa-clock opacity-50"></i>
                                                {{ $sermon->sermon_date ? $sermon->sermon_date->translatedFormat('d M, Y') : 'S/ Data' }}
                                            </span>
                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                            <span>{{ number_format($sermon->views) }} vistas</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap font-medium">
                                @if($sermon->category)
                                    <span class="px-3 py-1 text-[10px] font-extrabold rounded-full border border-slate-100 dark:border-slate-800" style="background-color: {{ $sermon->category->color ?? '#64748b' }}10; color: {{ $sermon->category->color ?? '#64748b' }}">
                                        {{ $sermon->category->name }}
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $sermon->user->avatar_url }}" alt="{{ $sermon->user->name }}" class="h-7 w-7 rounded-full object-cover shadow-sm bg-slate-100 ring-2 ring-slate-50 dark:ring-slate-800">
                                    <span class="text-xs text-slate-700 dark:text-slate-300 font-bold">{{ $sermon->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="px-3 py-1.5 text-[10px] font-extrabold rounded-xl border
                                    @if($sermon->status === 'published')
                                        bg-green-50 text-green-700 border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-900/30
                                    @elseif($sermon->status === 'draft')
                                        bg-slate-50 text-slate-600 border-slate-100 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700
                                    @else
                                        bg-slate-50 text-slate-400 border-slate-100 dark:bg-slate-800 dark:text-slate-500 dark:border-slate-700
                                    @endif">
                                    <i class="fa-pro fa-solid fa-circle text-[6px] mr-1.5 opacity-50"></i>
                                    {{ $sermon->status_display }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-400">
                                    @if($sermon->visibility === 'public')
                                        <i class="fa-pro fa-solid fa-earth-americas text-blue-500"></i>
                                    @elseif($sermon->visibility === 'members')
                                        <i class="fa-pro fa-solid fa-users text-blue-600"></i>
                                    @else
                                        <i class="fa-pro fa-solid fa-lock text-slate-400"></i>
                                    @endif
                                    {{ $sermon->visibility_display }}
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center gap-3">
                                    <a href="{{ route('admin.sermons.sermons.edit', $sermon) }}"
                                        class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all" title="Editar Esboço">
                                        <i class="fa-pro fa-solid fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.sermons.sermons.show', $sermon) }}"
                                        class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all" title="Visualizar">
                                        <i class="fa-pro fa-solid fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.sermons.sermons.destroy', $sermon) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Mover para o arquivo morto? Esta ação é irreversível.');">
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
                            <td colspan="6" class="px-6 py-20">
                                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                    <div class="w-20 h-20 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center mb-6">
                                        <i class="fa-pro fa-solid fa-scroll text-blue-500 text-3xl"></i>
                                    </div>
                                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-2">Cofre de Sermões Vazio</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">Prepare sua próxima mensagem com ferramentas profissionais de homilética e exegese.</p>
                                    <a href="{{ route('admin.sermons.sermons.create') }}"
                                        class="inline-flex items-center px-8 py-3.5 bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white text-sm font-extrabold rounded-2xl shadow-xl transition-all">
                                        <i class="fa-pro fa-solid fa-plus-circle mr-3"></i>
                                        Começar Estudo
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-950/20">
            {{ $sermons->links() }}
        </div>
    </div>
</div>
@endsection
