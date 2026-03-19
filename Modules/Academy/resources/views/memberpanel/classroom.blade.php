@extends('memberpanel::components.layouts.master')

@section('title', 'Academy | Sala de Aula')

@section('content')
    <div class="space-y-6 pb-10">
        <div class="rounded-3xl border border-slate-800 bg-slate-900 px-6 py-6 shadow-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-300">VEPL Academy</p>
            <h1 class="mt-2 text-2xl font-black text-white">{{ $course->title }}</h1>
            <p class="mt-3 max-w-4xl text-sm text-slate-300">{{ $course->description }}</p>
        </div>

        <div class="grid gap-6 xl:grid-cols-12">
            <section class="space-y-6 xl:col-span-8">
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="aspect-video bg-slate-950">
                        @if ($currentLesson?->video_url)
                            <iframe class="h-full w-full" src="{{ $currentLesson->video_url }}"
                                title="{{ $currentLesson->title }}" allowfullscreen></iframe>
                        @else
                            <div class="flex h-full items-center justify-center text-slate-300">
                                <div class="text-center">
                                    <x-icon name="video" class="mx-auto mb-2 h-8 w-8 opacity-80" />
                                    <p class="text-sm font-medium">Nenhum vídeo disponível para esta aula.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="space-y-4 p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">Aula atual</p>
                                <h2 class="mt-1 text-xl font-bold text-slate-900">{{ $currentLesson?->title ?? 'Selecione uma aula' }}</h2>
                            </div>
                            @if ($currentLesson)
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $currentLesson->is_free ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $currentLesson->is_free ? 'Aula gratuita' : 'Exclusiva para membros' }}
                                </span>
                            @endif
                        </div>
                        <article class="prose prose-slate max-w-none text-sm leading-7">
                            {!! nl2br(e($currentLesson?->content_text ?? 'O conteúdo textual desta aula estará disponível aqui.')) !!}
                        </article>
                    </div>
                </div>
            </section>

            <aside class="xl:col-span-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm" x-data="{ openModule: {{ $course->modules->first()?->id ?? 'null' }} }">
                    <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500">Módulos e aulas</h3>

                    <div class="space-y-3">
                        @foreach ($course->modules as $module)
                            <div class="rounded-xl border border-slate-200">
                                <button type="button"
                                    class="flex w-full items-center justify-between rounded-xl bg-slate-50 px-4 py-3 text-left text-sm font-semibold text-slate-800"
                                    @click="openModule = openModule === {{ $module->id }} ? null : {{ $module->id }}">
                                    <span>{{ $module->order }}. {{ $module->title }}</span>
                                    <x-icon name="chevron-down" class="h-4 w-4 transition-transform"
                                        x-bind:class="openModule === {{ $module->id }} ? 'rotate-180' : ''" />
                                </button>

                                <div x-show="openModule === {{ $module->id }}" x-transition class="space-y-1 px-3 py-3">
                                    @foreach ($module->lessons as $lesson)
                                        @php
                                            $isCurrent = $currentLesson && $currentLesson->id === $lesson->id;
                                            $isCompleted = in_array($lesson->id, $completedLessonIds, true);
                                        @endphp
                                        <a href="{{ route('memberpanel.academy.classroom.lesson', ['course' => $course->id, 'lesson' => $lesson->id]) }}"
                                            class="flex items-center justify-between rounded-lg px-3 py-2 text-sm transition {{ $isCurrent ? 'bg-amber-100 text-amber-900' : 'hover:bg-slate-100 text-slate-700' }}">
                                            <span class="line-clamp-1">{{ $lesson->title }}</span>
                                            @if ($isCompleted)
                                                <x-icon name="circle-check" class="h-4 w-4 text-emerald-600" />
                                            @else
                                                <x-icon name="circle" class="h-4 w-4 text-slate-400" />
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection
