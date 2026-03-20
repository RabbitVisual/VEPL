@extends('admin::components.layouts.master')

@section('title', 'Correções Strong (Aprovação Admin)')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Correções Oficiais do Strong</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Aprove ou rejeite correções teológicas pendentes. Ao aprovar, aplicamos automaticamente no léxico.</p>
            </div>
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

        <div class="flex flex-wrap gap-2">
            @php
                $items = [
                    'pending' => 'Pendentes',
                    'approved' => 'Aprovadas',
                    'rejected' => 'Rejeitadas',
                    'all' => 'Todas',
                ];
            @endphp
            @foreach($items as $key => $label)
                <a href="{{ request()->url() }}?status={{ $key }}"
                   class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors border
                   {{ $status === $key
                        ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 border-blue-200 dark:border-blue-800'
                        : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                    {{ $label }}
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                        {{ $key === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                         : ($key === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'
                         : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300') }}">
                        {{ $key === 'all' ? array_sum($counts) : ($counts[$key] ?? 0) }}
                    </span>
                </a>
            @endforeach
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Lista</h2>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Página {{ $corrections->currentPage() }} / {{ $corrections->lastPage() }}
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/30">
                        <tr class="text-left text-gray-600 dark:text-gray-300">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Strong</th>
                            <th class="px-4 py-3">Campo</th>
                            <th class="px-4 py-3">Proposta</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Solicitante</th>
                            <th class="px-4 py-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($corrections as $correction)
                            <tr class="align-top">
                                <td class="px-4 py-3 font-mono text-gray-900 dark:text-white">{{ $correction->id }}</td>
                                <td class="px-4 py-3 font-mono text-blue-700 dark:text-blue-300">{{ $correction->strong_number }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $correction->field }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">
                                    {{ \Illuminate\Support\Str::limit((string) $correction->proposed_value, 60) }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $badge = match($correction->status) {
                                            'approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                            'rejected' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
                                            default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                        };
                                    @endphp
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $badge }}">
                                        {{ $correction->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">
                                    {{ $correction->requester?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.bible.strongs-corrections.show', $correction->id) }}"
                                       class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-semibold bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Nenhuma correção encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $corrections->links() }}
            </div>
        </div>
    </div>
@endsection

