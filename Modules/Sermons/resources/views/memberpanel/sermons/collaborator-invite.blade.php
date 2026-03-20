@extends('memberpanel::components.layouts.master')

@section('title', 'Convite para co-autoria')

@section('content')
    <div class="mx-auto max-w-2xl py-8 px-4">
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-100 bg-slate-50 p-6 dark:border-gray-700 dark:bg-slate-900/40">
                <h1 class="text-2xl font-black text-gray-900 dark:text-white mb-1">Convite para coautoria</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Leia os detalhes e responda com segurança.</p>
            </div>
            <div class="p-6">
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                Você foi convidado para colaborar no sermão <strong>"{{ $collaborator->sermon->title }}"</strong>,
                de <strong>{{ $collaborator->sermon->user->name ?? 'N/A' }}</strong>. Ao aceitar, você poderá contribuir no conteúdo juntamente com o autor.
                </p>
                <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900/40 dark:bg-amber-900/20 dark:text-amber-300">
                    Ao aceitar, você ganha permissão para editar o sermão e colaborar no manuscrito com o autor principal.
                </div>
                <form method="post" action="{{ route('memberpanel.sermons.collaborator.respond', $collaborator) }}" class="flex flex-col gap-3 sm:flex-row">
                    @csrf
                    <button type="submit" name="action" value="accept"
                        class="flex-1 rounded-xl bg-amber-600 px-4 py-3 font-bold text-white hover:bg-amber-700 transition-colors">Aceitar convite</button>
                    <button type="submit" name="action" value="reject"
                        class="flex-1 rounded-xl border border-gray-300 px-4 py-3 font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">Recusar</button>
                </form>
            </div>
        </div>
    </div>
@endsection
