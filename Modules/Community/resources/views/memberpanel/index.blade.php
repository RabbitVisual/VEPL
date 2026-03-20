@extends('memberpanel::components.layouts.master')

@section('title', 'Community | Discussões')

@section('content')
    <div class="space-y-6 pb-10">
        <div class="rounded-3xl border border-slate-800 bg-slate-900 px-6 py-6 shadow-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-300">Rede de Pastores VEPL</p>
            <h1 class="mt-2 text-2xl font-black text-white">Discussões Teológicas</h1>
            <p class="mt-3 max-w-4xl text-sm text-slate-300">
                Debates com foco bíblico, clareza textual e construção pastoral prática.
            </p>
        </div>

        <div class="space-y-4">
            @forelse ($topics as $topic)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">
                            {{ $topic->category->name }}
                        </span>
                        <span>{{ $topic->created_at?->diffForHumans() }}</span>
                    </div>
                    <h2 class="mt-3 text-lg font-bold text-slate-900">{{ $topic->title }}</h2>
                    <p class="mt-2 line-clamp-3 text-sm leading-7 text-slate-700">
                        {!! nl2br(app(\Modules\Bible\App\Services\BibleReferenceParserService::class)->parseText(strip_tags($topic->body))) !!}
                    </p>
                    <div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-slate-500">
                        <span class="inline-flex items-center gap-1">
                            <x-icon name="user" class="h-4 w-4" />
                            {{ $topic->user->name ?? 'Autor desconhecido' }}
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <x-icon name="comment" class="h-4 w-4" />
                            {{ $topic->replies->count() }} respostas
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <x-icon name="eye" class="h-4 w-4" />
                            {{ $topic->views_count }} visualizações
                        </span>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
                    <h3 class="text-base font-semibold text-slate-900">Nenhuma discussão publicada ainda.</h3>
                    <p class="mt-2 text-sm text-slate-600">As primeiras conversas da comunidade aparecerão aqui.</p>
                </div>
            @endforelse
        </div>

        <div>
            {{ $topics->links() }}
        </div>
    </div>
@endsection
