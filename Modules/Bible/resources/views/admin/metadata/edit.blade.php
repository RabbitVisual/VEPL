@extends('admin::components.layouts.master')

@section('title', 'Editar Comentário Oficial')

@section('content')
    <div class="min-h-screen bg-slate-50 p-6">
        <div class="mx-auto max-w-6xl space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Editar Comentário Oficial</h1>
                <p class="mt-1 text-sm text-slate-600">
                    {{ $item->book?->name }} {{ $item->chapter?->chapter_number }}:{{ $item->verse?->verse_number }}
                </p>
            </div>

            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif

            @include('bible::admin.metadata._form')
        </div>
    </div>
@endsection
