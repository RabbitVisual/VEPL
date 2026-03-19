@extends('homepage::components.layouts.master')

@section('content')
@if ($importantNotifications->count() > 0)
    <div id="notification-bar" class="relative z-50 bg-slate-800 border-b border-slate-700 shadow-md hidden" style="min-height: 44px;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center gap-3 py-2">
            <span class="shrink-0 flex h-2 w-2 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400/75"></span>
                <span class="relative inline-flex rounded-full h-full w-full bg-amber-500"></span>
            </span>
            <span class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 hidden sm:block">Aviso</span>
            <div class="flex-1 min-w-0 relative overflow-hidden" style="height: 32px;">
                @foreach($importantNotifications as $i => $notification)
                    <div class="notif-item absolute inset-0 flex items-center gap-3 transition-all duration-500 {{ $i === 0 ? 'translate-y-0 opacity-100' : 'translate-y-full opacity-0' }}" data-index="{{ $i }}">
                        <span class="font-semibold text-white text-sm truncate shrink-0">{{ $notification->title }}</span>
                        @if($notification->message)
                            <span class="text-slate-300 text-xs line-clamp-1 hidden md:block border-l border-slate-600 pl-3">{{ Str::limit($notification->message, 80) }}</span>
                        @endif
                        @if($notification->action_url)
                            <a href="{{ $notification->action_url }}" class="shrink-0 bg-amber-600 hover:bg-amber-500 text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full transition whitespace-nowrap">
                                {{ $notification->action_text ?: 'Ver mais' }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
            @if($importantNotifications->count() > 1)
                <div class="shrink-0 flex items-center gap-1.5">
                    <button id="notif-prev" class="w-5 h-5 rounded-full bg-slate-600 hover:bg-slate-500 text-white flex items-center justify-center transition" aria-label="Anterior">
                        <x-icon name="chevron-left" class="w-3 h-3" />
                    </button>
                    <span id="notif-counter" class="text-[10px] font-bold text-slate-400 w-8 text-center tabular-nums">1/{{ $importantNotifications->count() }}</span>
                    <button id="notif-next" class="w-5 h-5 rounded-full bg-slate-600 hover:bg-slate-500 text-white flex items-center justify-center transition" aria-label="Próximo">
                        <x-icon name="chevron-right" class="w-3 h-3" />
                    </button>
                </div>
            @endif
            <button id="close-notif-bar" class="shrink-0 ml-1 w-7 h-7 rounded-full bg-slate-700 hover:bg-slate-600 text-white flex items-center justify-center transition" aria-label="Fechar">
                <x-icon name="xmark" class="w-4 h-4" />
            </button>
        </div>
    </div>
    <script>
    (function() {
        const KEY = 'vepl_notif_bar_dismissed';
        const bar = document.getElementById('notification-bar');
        const items = Array.from(document.querySelectorAll('.notif-item'));
        const counter = document.getElementById('notif-counter');
        const prevBtn = document.getElementById('notif-prev');
        const nextBtn = document.getElementById('notif-next');
        const closeBtn = document.getElementById('close-notif-bar');
        let current = 0;
        if (!localStorage.getItem(KEY)) bar.classList.remove('hidden');
        function showItem(index, direction) {
            const outgoing = items[current], incoming = items[index];
            outgoing.style.transition = 'none'; outgoing.style.transform = 'translateY(0)'; outgoing.style.opacity = '1';
            requestAnimationFrame(() => {
                outgoing.style.transition = 'all 0.38s cubic-bezier(0.4,0,0.2,1)';
                outgoing.style.transform = direction === 'next' ? 'translateY(-100%)' : 'translateY(100%)';
                outgoing.style.opacity = '0';
                incoming.style.transition = 'none';
                incoming.style.transform = direction === 'next' ? 'translateY(100%)' : 'translateY(-100%)';
                incoming.style.opacity = '0';
                requestAnimationFrame(() => {
                    incoming.style.transition = 'all 0.38s cubic-bezier(0.4,0,0.2,1)';
                    incoming.style.transform = 'translateY(0)';
                    incoming.style.opacity = '1';
                });
            });
            current = index;
            if (counter) counter.textContent = (current + 1) + '/{{ $importantNotifications->count() }}';
        }
        if (nextBtn) nextBtn.addEventListener('click', () => showItem((current + 1) % items.length, 'next'));
        if (prevBtn) prevBtn.addEventListener('click', () => showItem((current - 1 + items.length) % items.length, 'prev'));
        @if($importantNotifications->count() > 1)
        setInterval(() => { if (!document.hidden) showItem((current + 1) % items.length, 'next'); }, 5000);
        @endif
        closeBtn.addEventListener('click', () => {
            bar.style.transition = 'opacity 0.3s'; bar.style.opacity = '0';
            setTimeout(() => { bar.style.display = 'none'; localStorage.setItem(KEY, '1'); }, 300);
        });
    })();
    </script>
@endif

<div class="bg-slate-50 text-slate-900">
    {{-- A. Hero Section (Dinâmica) --}}
    @if ($carouselEnabled && $carouselSlides->count() > 0)
        <section class="hero-section relative w-full min-h-[70vh] md:min-h-[85vh] flex items-center bg-slate-900">
            <div id="homepage-carousel" class="relative w-full" data-carousel="{{ $homepageSettings['carousel_autoplay'] ? 'slide' : 'static' }}" @if($homepageSettings['carousel_autoplay']) data-carousel-interval="{{ $homepageSettings['carousel_interval'] }}" @endif>
                <div class="relative min-h-[70vh] md:min-h-[85vh] overflow-hidden">
                    @foreach ($carouselSlides as $index => $slide)
                        <div class="{{ $index === 0 ? '' : 'hidden' }} absolute inset-0 transition-opacity duration-700" data-carousel-item>
                            @if ($slide->image_url)
                                <img src="{{ $slide->image_url }}" alt="{{ $slide->alt_text ?? $slide->title }}" class="absolute inset-0 h-full w-full object-cover">
                            @else
                                <div class="absolute inset-0 bg-slate-900"></div>
                            @endif

                            <div class="absolute inset-0 bg-black/60"></div>

                            <div class="relative z-10 mx-auto flex min-h-[70vh] md:min-h-[85vh] max-w-7xl items-center px-4 py-20 sm:px-6 lg:px-8">
                                <div class="max-w-3xl text-left text-slate-50">
                                    <p class="mb-4 inline-flex items-center rounded-full border border-amber-500/40 bg-amber-500/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-300">
                                        Escola de Pastores e Lideres
                                    </p>

                                    <h1 class="text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                                        {{ $slide->title ?: ($homepageSettings['hero_title'] ?? 'VEPL') }}
                                    </h1>

                                    <p class="mt-6 max-w-2xl text-base text-slate-200 sm:text-lg">
                                        {{ $slide->description ?: ($homepageSettings['hero_subtitle'] ?? '') }}
                                    </p>

                                    <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                                        @if ($slide->link && $slide->link_text)
                                            <a href="{{ $slide->link }}" class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-8 py-4 text-base font-bold text-white shadow-lg shadow-amber-800/30 transition hover:bg-amber-500">
                                                {{ $slide->link_text }}
                                            </a>
                                        @else
                                            <a href="{{ auth()->check() ? route('memberpanel.dashboard') : route('login') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-8 py-4 text-base font-bold text-white shadow-lg shadow-amber-800/30 transition hover:bg-amber-500">
                                                Acessar a Plataforma
                                            </a>
                                        @endif

                                        <a href="#cursos" class="inline-flex items-center justify-center rounded-xl border border-slate-200/30 bg-slate-900/30 px-8 py-4 text-base font-semibold text-slate-100 transition hover:bg-slate-800/60">
                                            Ver Cursos
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($homepageSettings['carousel_indicators'] ?? true)
                    <div class="absolute bottom-8 left-1/2 z-30 flex -translate-x-1/2 gap-2">
                        @foreach ($carouselSlides as $i => $s)
                            <button type="button" class="h-2.5 w-2.5 rounded-full {{ $i === 0 ? 'bg-amber-500' : 'bg-white/50 hover:bg-white' }} transition" data-carousel-slide-to="{{ $i }}" aria-label="Slide {{ $i + 1 }}"></button>
                        @endforeach
                    </div>
                @endif

                @if($homepageSettings['carousel_controls'] ?? true)
                    <button type="button" class="absolute left-4 top-1/2 z-30 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-slate-900/40 text-white transition hover:bg-slate-800/70" data-carousel-prev>
                        <x-icon name="chevron-left" class="h-5 w-5" />
                    </button>
                    <button type="button" class="absolute right-4 top-1/2 z-30 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-slate-900/40 text-white transition hover:bg-slate-800/70" data-carousel-next>
                        <x-icon name="chevron-right" class="h-5 w-5" />
                    </button>
                @endif
            </div>
        </section>
    @else
        <section class="hero-section relative min-h-[70vh] md:min-h-[85vh] overflow-hidden bg-slate-900">
            @if($homepageSettings['hero_bg_image'] ?? null)
                <img src="{{ asset($homepageSettings['hero_bg_image']) }}" alt="VEPL Hero" class="absolute inset-0 h-full w-full object-cover">
            @endif
            <div class="absolute inset-0 bg-black/60"></div>

            <div class="relative z-10 mx-auto flex min-h-[70vh] md:min-h-[85vh] max-w-7xl items-center px-4 py-20 sm:px-6 lg:px-8">
                <div class="max-w-3xl text-left text-slate-50">
                    <p class="mb-4 inline-flex items-center rounded-full border border-amber-500/40 bg-amber-500/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-300">
                        Formacao Teologica de Alta Performance
                    </p>

                    <h1 class="text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                        {{ $homepageSettings['hero_title'] }}
                    </h1>

                    <p class="mt-6 max-w-2xl text-base text-slate-200 sm:text-lg">
                        {{ $homepageSettings['hero_subtitle'] }}
                    </p>

                    <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                        <a href="{{ auth()->check() ? route('memberpanel.dashboard') : route('login') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-8 py-4 text-base font-bold text-white shadow-lg shadow-amber-800/30 transition hover:bg-amber-500">
                            Acessar a Plataforma
                        </a>
                        <a href="#cursos" class="inline-flex items-center justify-center rounded-xl border border-slate-200/30 bg-slate-900/30 px-8 py-4 text-base font-semibold text-slate-100 transition hover:bg-slate-800/60">
                            Matricule-se
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- B. Pilares da VEPL (Estática) --}}
    <section id="pilares" class="bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-amber-600">Diferenciais da Escola</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                    Pilares da VEPL
                </h2>
                <p class="mt-4 text-slate-600">
                    Uma arquitetura educacional pensada para formar lideres biblicos com profundidade, clareza pastoral e aplicacao ministerial.
                </p>
            </div>

            <div class="mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['icon' => 'graduation-cap', 'title' => 'Academia Teologica', 'desc' => 'Cursos e trilhas estruturadas para formacao pastoral consistente, progressiva e contextualizada.'],
                    ['icon' => 'users', 'title' => 'Comunidade Pastoral', 'desc' => 'Networking ministerial, foruns de discussao e colaboracao entre lideres em diferentes niveis de atuacao.'],
                    ['icon' => 'magnifying-glass', 'title' => 'Nepe Search', 'desc' => 'Pesquisa exegética orientada para preparar estudos, aulas e mensagens com agilidade e confiabilidade.'],
                    ['icon' => 'book-bible', 'title' => 'Sermoes Expositivos', 'desc' => 'Biblioteca de recursos para o pulpito com base biblica, estrutura homiletica e aplicacao pastoral.'],
                ] as $pilar)
                    <article class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-amber-500/50 hover:shadow-xl">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 transition group-hover:bg-amber-600">
                            <x-icon name="{{ $pilar['icon'] }}" class="h-6 w-6 text-amber-700 transition group-hover:text-white" />
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">{{ $pilar['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $pilar['desc'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- C. Cursos em Destaque (Dinâmica - Novo) --}}
    @if ($featuredCourses->isNotEmpty())
        <section id="cursos" class="bg-white py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-700">Cursos Recentes</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                        Formacao Ativa na Plataforma
                    </h2>
                </div>

                <div class="mt-14 grid grid-cols-1 gap-8 md:grid-cols-3">
                    @foreach ($featuredCourses as $course)
                        @php
                            $levelLabels = [
                                'basic' => 'Basico',
                                'intermediate' => 'Intermediario',
                                'advanced' => 'Avancado',
                            ];
                            $levelLabel = $levelLabels[$course->level] ?? ucfirst((string) $course->level);
                        @endphp
                        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                            @if ($course->cover_image)
                                <img src="{{ asset('storage/' . ltrim($course->cover_image, '/')) }}" alt="{{ $course->title }}" class="h-48 w-full object-cover">
                            @else
                                <div class="flex h-48 w-full items-center justify-center bg-slate-200">
                                    <x-icon name="graduation-cap" class="h-14 w-14 text-slate-400" />
                                </div>
                            @endif

                            <div class="p-6">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="rounded-full bg-blue-700/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-blue-700">
                                        {{ $levelLabel }}
                                    </span>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        {{ (int) $course->workload_hours }}h
                                    </span>
                                </div>

                                <h3 class="mt-4 text-xl font-bold text-slate-900">{{ $course->title }}</h3>
                                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ Str::limit($course->description ?? 'Curso de formacao ministerial na plataforma VEPL.', 120) }}</p>

                                <div class="mt-6">
                                    @if (Route::has('memberpanel.academy.classroom'))
                                        <a href="{{ route('memberpanel.academy.classroom', ['course' => $course->id]) }}" class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-amber-500">
                                            Saiba Mais
                                            <x-icon name="arrow-right" class="h-4 w-4" />
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-amber-500">
                                            Saiba Mais
                                            <x-icon name="arrow-right" class="h-4 w-4" />
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- D. Impacto e Métricas (Dinâmica) --}}
    @if (($homepageSettings['show_statistics'] ?? true) && $statistics)
        <section id="metricas" class="bg-slate-900 py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-amber-400">Impacto VEPL</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-50 sm:text-4xl">
                        {{ $homepageSettings['statistics_title'] ?? 'Impacto da Formacao VEPL' }}
                    </h2>
                </div>

                <div class="mt-14 grid grid-cols-2 gap-5 md:grid-cols-3 lg:grid-cols-6">
                    @foreach([
                        ['key' => 'sermons', 'label' => 'Sermoes', 'icon' => 'book-bible'],
                        ['key' => 'events', 'label' => 'Eventos', 'icon' => 'calendar'],
                        ['key' => 'bible_resources', 'label' => 'Recursos Biblicos', 'icon' => 'book-open'],
                        ['key' => 'ministries', 'label' => 'Ministerios', 'icon' => 'users'],
                        ['key' => 'members', 'label' => 'Lideres', 'icon' => 'graduation-cap'],
                        ['key' => 'years', 'label' => 'Anos', 'icon' => 'clock'],
                    ] as $stat)
                        @if (isset($statistics[$stat['key']]))
                            <article class="rounded-xl border border-slate-700 bg-slate-800/60 p-5 text-center">
                                <x-icon name="{{ $stat['icon'] }}" class="mx-auto h-7 w-7 text-amber-500" />
                                <p class="mt-3 text-3xl font-black tabular-nums text-amber-500">
                                    {{ number_format((int) $statistics[$stat['key']]) }}
                                </p>
                                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-300">
                                    {{ $stat['label'] }}
                                </p>
                            </article>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- E. Depoimentos de Líderes (Dinâmica) --}}
    @if (($homepageSettings['show_testimonials'] ?? true) && $activeTestimonials->isNotEmpty())
        <section id="depoimentos" class="bg-slate-50 py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-amber-600">Testemunhos de Lideres</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                        {{ $homepageSettings['testimonials_title'] ?? 'Depoimentos de Lideres' }}
                    </h2>
                </div>

                <div class="mt-14 grid grid-cols-1 gap-8 md:grid-cols-3">
                    @foreach ($activeTestimonials as $testimonial)
                        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-4 flex items-center justify-between">
                                <x-icon name="quote-left" class="h-6 w-6 text-slate-400" />
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Lideranca</span>
                            </div>

                            <p class="text-sm leading-relaxed text-slate-700">
                                "{{ Str::limit($testimonial->testimonial, 190) }}"
                            </p>

                            <div class="mt-6 flex items-center gap-4 border-t border-slate-100 pt-5">
                                @if ($testimonial->photo)
                                    <img src="{{ asset('storage/' . ltrim($testimonial->photo, '/')) }}" alt="{{ $testimonial->name }}" class="h-12 w-12 rounded-full object-cover">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100">
                                        <x-icon name="user" class="h-5 w-5 text-amber-700" />
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="truncate font-bold text-slate-900">{{ $testimonial->name }}</p>
                                    <p class="truncate text-xs text-slate-500">
                                        {{ $testimonial->ministerial_title ?: ($testimonial->position ?: 'Lider') }}
                                        @if($testimonial->church_affiliation)
                                            · {{ $testimonial->church_affiliation }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- F. Rodapé e CTA Final --}}
    <section id="cta" class="bg-slate-900 py-20">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-amber-400">Proximo Passo Ministerial</p>
            <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-50 sm:text-4xl">
                Faca parte da Escola VEPL
            </h2>
            <p class="mx-auto mt-5 max-w-2xl text-slate-300">
                Entre na plataforma e acelere sua jornada de formacao pastoral com uma comunidade comprometida com excelencia biblica e lideranca servidora.
            </p>

            <div class="mt-10 flex flex-col justify-center gap-4 sm:flex-row">
                @auth
                    <a href="{{ route('memberpanel.dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-8 py-4 text-base font-bold text-white transition hover:bg-amber-500">
                        Ir para o Painel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-8 py-4 text-base font-bold text-white transition hover:bg-amber-500">
                        Entrar
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-8 py-4 text-base font-bold text-white transition hover:bg-blue-600">
                        Matricule-se
                    </a>
                @endauth
            </div>
        </div>
    </section>
</div>
@endsection
