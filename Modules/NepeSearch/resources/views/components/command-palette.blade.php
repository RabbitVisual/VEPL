<div x-data="nepeCommandPalette()" x-on:keydown.window.prevent.ctrl.k="openPalette()" x-on:keydown.window.prevent.meta.k="openPalette()">
    <div x-show="open" x-transition.opacity class="fixed inset-0 z-[90] bg-slate-950/60 backdrop-blur-sm" @click="closePalette()"></div>

    <div x-show="open" x-transition class="fixed inset-0 z-[100] flex items-start justify-center px-4 pt-20">
        <div class="w-full max-w-3xl rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200">
            <div class="border-b border-slate-200 p-4">
                <input type="text" x-model="query" @input.debounce.300ms="search()"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-lg text-slate-900 outline-none ring-amber-600 transition focus:ring-2"
                    placeholder="Buscar sermões, versículos, aulas e discussões..." />
            </div>

            <div class="max-h-[65vh] overflow-y-auto p-4 space-y-5">
                <template x-for="group in groups" :key="group.key">
                    <section x-show="results[group.key]?.length">
                        <h3 class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-500" x-text="group.label"></h3>
                        <div class="space-y-2">
                            <template x-for="item in results[group.key]" :key="item.id">
                                <div class="rounded-lg border border-slate-200 px-3 py-2">
                                    <p class="text-sm font-semibold text-slate-900" x-text="item.title || item.full_reference || 'Resultado'"></p>
                                    <p class="mt-1 text-xs text-slate-600 line-clamp-2" x-text="item.description || item.text || item.body || item.content_text || ''"></p>
                                </div>
                            </template>
                        </div>
                    </section>
                </template>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            function nepeCommandPalette() {
                return {
                    open: false,
                    query: '',
                    loading: false,
                    results: {},
                    groups: [
                        { key: 'sermons', label: 'Sermões Encontrados' },
                        { key: 'bible', label: 'Versículos Encontrados' },
                        { key: 'academy_courses', label: 'Cursos Encontrados' },
                        { key: 'academy_lessons', label: 'Aulas Encontradas' },
                        { key: 'community_topics', label: 'Discussões Encontradas' },
                    ],
                    openPalette() {
                        this.open = true;
                        this.$nextTick(() => this.search());
                    },
                    closePalette() {
                        this.open = false;
                    },
                    async search() {
                        if (!this.query.trim()) {
                            this.results = {};
                            return;
                        }
                        this.loading = true;
                        const response = await fetch(`{{ route('memberpanel.nepesearch.search') }}?q=${encodeURIComponent(this.query)}`);
                        this.results = await response.json();
                        this.loading = false;
                    }
                };
            }
        </script>
    @endpush
@endonce
