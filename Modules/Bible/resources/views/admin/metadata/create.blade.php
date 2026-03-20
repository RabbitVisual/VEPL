@extends('admin::components.layouts.master')

@section('title', 'Novo Comentário Oficial')

@section('content')
    <div class="min-h-screen bg-slate-50 p-6">
        <div class="mx-auto max-w-6xl space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Novo Comentário Oficial</h1>
                <p class="mt-1 text-sm text-slate-600">Selecione a referência e redija o comentário oficial da VEPL.</p>
            </div>

            @include('bible::admin.metadata._form')
        </div>
    </div>
@endsection
