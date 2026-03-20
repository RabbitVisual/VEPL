@extends('admin::components.layouts.master')

@section('title', 'Correção Strong #'.$correction->id)

@section('content')
    <div class="p-6 space-y-6">
        @php($bibleRefs = app(\Modules\Bible\App\Services\BibleReferenceParserService::class))
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Correção Strong #{{ $correction->id }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    Strong <span class="font-mono text-blue-600 dark:text-blue-400">{{ $correction->strong_number }}</span> · campo: <span class="font-semibold">{{ $correction->field }}</span>
                </p>
            </div>
            <a href="{{ route('admin.bible.strongs-corrections.index') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" />
                Voltar
            </a>
        </div>

        @if ($correction->status === 'pending')
            <div class="p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-lg dark:bg-amber-900/20 dark:border-amber-900/40 dark:text-amber-200">
                Esta correção está <span class="font-bold">PENDENTE</span>. Você pode aprovar (aplica no lexicon) ou rejeitar.
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Detalhes</h2>
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold
                    {{ $correction->status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' :
                       ($correction->status === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300' :
                       'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300') }}">
                    {{ $correction->status }}
                </span>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Atual</p>
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                            <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">
                                {!! $bibleRefs->parseText(e((string) $correction->current_value)) !!}
                            </p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Proposta</p>
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                            <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">
                                {!! $bibleRefs->parseText(e((string) $correction->proposed_value)) !!}
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Justificativa</p>
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                        <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">
                            {!! $bibleRefs->parseText(e((string) $correction->justification)) !!}
                        </p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Solicitante</p>
                        <p class="text-sm text-gray-700 dark:text-gray-200">
                            {{ $correction->requester?->name ?? '—' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            {{ $correction->created_at?->format('d M, Y H:i') }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Revisor (se houver)</p>
                        <p class="text-sm text-gray-700 dark:text-gray-200">
                            {{ $correction->reviewer?->name ?? '—' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            {{ $correction->reviewed_at?->format('d M, Y H:i') ?? '—' }}
                        </p>
                    </div>
                </div>

                @if ($correction->admin_notes)
                    <div class="rounded-lg border border-blue-200 bg-blue-50 dark:border-blue-900/40 dark:bg-blue-900/20 p-4">
                        <p class="text-xs text-blue-700 dark:text-blue-300 uppercase tracking-wider mb-2">Admin notes</p>
                        <p class="text-sm text-blue-900 dark:text-blue-200 whitespace-pre-wrap">{!! $bibleRefs->parseText(e((string) $correction->admin_notes)) !!}</p>
                    </div>
                @endif

                @if ($correction->status === 'pending')
                    <div class="grid md:grid-cols-2 gap-6">
                        <form action="{{ route('admin.bible.strongs-corrections.approve', $correction->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Notes (obrigatório)</label>
                                <textarea name="admin_notes" rows="5" required
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors"
                                          placeholder="Explique por que aprovou esta correção."></textarea>
                            </div>
                            <button type="submit"
                                    class="w-full px-4 py-2.5 text-sm font-medium text-white bg-linear-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                                Aprovar e aplicar
                            </button>
                        </form>

                        <form action="{{ route('admin.bible.strongs-corrections.reject', $correction->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Notes (obrigatório)</label>
                                <textarea name="admin_notes" rows="5" required
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors"
                                          placeholder="Explique por que rejeitou esta correção."></textarea>
                            </div>
                            <button type="submit"
                                    class="w-full px-4 py-2.5 text-sm font-medium text-white bg-linear-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                                Rejeitar
                            </button>
                        </form>
                    </div>
                @endif

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-extrabold text-gray-900 dark:text-white uppercase tracking-widest mb-3">Entrada atual do léxico</h3>
                    @if($lexicon)
                        <div class="space-y-2 text-sm text-gray-700 dark:text-gray-200">
                            <div><span class="font-semibold">Lemma:</span> {{ $lexicon->lemma }}</div>
                            <div><span class="font-semibold">Pronúncia:</span> {{ $lexicon->pronounce }}</div>
                            <div><span class="font-semibold">XLit:</span> {{ $lexicon->xlit }}</div>
                            <div><span class="font-semibold">Descrição PT:</span></div>
                            <div class="whitespace-pre-wrap text-gray-600 dark:text-gray-300">{!! $bibleRefs->parseText(e((string) $lexicon->description_pt)) !!}</div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Entrada do Strong não encontrada.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

