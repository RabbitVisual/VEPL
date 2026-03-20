@extends('admin::components.layouts.master')

@section('title', 'Esboços Homiléticos - Administração')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Esboços Homiléticos</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                <x-icon name="graduation-cap" style="solid" class="text-blue-500" />
                Gestão de Materiais de Discipulado & Acadêmicos
            </p>
        </div>
        <a href="{{ route('admin.sermons.studies.create') }}"
            class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-extrabold rounded-2xl shadow-lg shadow-blue-500/20 text-white bg-blue-600 hover:bg-blue-700 transform hover:-translate-y-0.5 transition-all">
            <x-icon name="plus-circle" style="solid" class="mr-2" />
            Novo Estudo
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
        <form method="GET" action="{{ route('admin.sermons.studies.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative">
                <x-icon name="magnifying-glass" style="solid" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs" />
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full pl-9 pr-3 py-2.5 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all"
                    placeholder="Pesquisar...">
            </div>

            <select name="sermon_series_id" class="px-3 py-2.5 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all font-bold">
                <option value="">Todas as Séries</option>
                @foreach($series as $s)
                    <option value="{{ $s->id }}" {{ request('sermon_series_id') == $s->id ? 'selected' : '' }}>
                        {{ $s->title }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="px-3 py-2.5 border border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all font-bold">
                <option value="">Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arquivado</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-slate-900 dark:bg-blue-600 text-white font-bold rounded-xl hover:bg-slate-800 dark:hover:bg-blue-700 transition-all shadow-md">
                    Filtrar
                </button>
                @if (request()->hasAny(['search', 'sermon_series_id', 'status']))
                    <a href="{{ route('admin.sermons.studies.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                        <x-icon name="rotate-left" style="solid" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Studies List -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50/50 dark:bg-slate-950/20">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Esboço Homilético</th>
                        <th class="px-6 py-4 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Série Acadêmica</th>
                        <th class="px-6 py-4 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Autor</th>
                        <th class="px-6 py-4 text-left text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Gestão</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($studies as $study)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 flex-shrink-0 relative group">
                                        @if($study->cover_image)
                                            <img class="h-12 w-12 rounded-xl object-cover shadow-sm group-hover:scale-105 transition-transform" src="{{ asset('storage/' . $study->cover_image) }}" alt="">
                                        @else
                                            <div class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center border border-slate-200 dark:border-slate-800 text-slate-400">
                                                <x-icon name="book-sparkles" style="solid" class="text-lg" />
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $study->title }}</div>
                                        <div class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center gap-2 mt-0.5">
                                            <span class="font-bold flex items-center gap-1">
                                                <x-icon name="clock" style="solid" class="opacity-50" />
                                                {{ $study->created_at->translatedFormat('d M, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-slate-600 dark:text-slate-400">
                                {{ $study->series ? $study->series->title : '-' }}
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $study->user->avatar_url }}" alt="{{ $study->user->name }}" class="h-7 w-7 rounded-full object-cover shadow-sm bg-slate-100 ring-2 ring-slate-50 dark:ring-slate-800">
                                    <span class="text-xs text-slate-700 dark:text-slate-300 font-bold">{{ $study->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="px-3 py-1.5 text-[10px] font-extrabold rounded-xl border
                                    @if($study->status === 'published')
                                        bg-green-50 text-green-700 border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-900/30
                                    @elseif($study->status === 'draft')
                                        bg-slate-50 text-slate-600 border-slate-100 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700
                                    @else
                                        bg-slate-50 text-slate-400 border-slate-100 dark:bg-slate-800 dark:text-slate-500 dark:border-slate-700
                                    @endif">
                                    <x-icon name="circle" style="solid" class="text-[6px] mr-1.5 opacity-50" />
                                    {{ $study->status === 'published' ? 'Publicado' : ($study->status === 'draft' ? 'Rascunho' : 'Arquivado') }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center gap-3">
                                    <a href="{{ route('admin.sermons.studies.edit', $study) }}"
                                        class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all" title="Editar Estudo">
                                        <x-icon name="edit" style="solid" />
                                    </a>
                                    <form action="{{ route('admin.sermons.studies.destroy', $study) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Excluir permanentemente este estudo acadêmico?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all" title="Deletar">
                                            <x-icon name="trash-can" style="solid" />
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
                                        <x-icon name="book-sparkles" style="solid" class="text-blue-500 text-3xl" />
                                    </div>
                                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-2">Sem Estudos Registrados</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">Inicie a criação de materiais teológicos profundos para o crescimento da igreja.</p>
                                    <a href="{{ route('admin.sermons.studies.create') }}"
                                        class="inline-flex items-center px-8 py-3.5 bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white text-sm font-extrabold rounded-2xl shadow-xl transition-all">
                                        <x-icon name="plus-circle" style="solid" class="mr-3" />
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
            {{ $studies->links() }}
        </div>
    </div>
</div>
@endsection
