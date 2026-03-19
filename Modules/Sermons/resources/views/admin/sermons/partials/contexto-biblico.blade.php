{{-- Painel lateral "Contexto Bíblico" (Panorama AT/NT). Requer $bibleBooks com book_number. --}}
<div x-data="contextoBiblico()" class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
    <button @click="open = !open" class="w-full px-5 py-4 flex items-center justify-between text-left font-extrabold text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
        <span class="flex items-center gap-3">
            <i class="fa-pro fa-solid fa-scroll-old text-blue-500"></i>
            Contexto Bíblico
            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 text-[10px] font-bold cursor-help" title="Autor, data, tema e destinatários do livro selecionado (Panorama).">?</span>
        </span>
        <i class="fa-pro fa-solid fa-chevron-down text-[10px] transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="border-t border-slate-100 dark:border-slate-800">
        <div class="p-5 space-y-5">
            <div>
                <label class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Livro em Análise</label>
                <select x-model="selectedBookNumber" @change="fetchPanorama()"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all">
                    <option value="">Selecione...</option>
                    @foreach($bibleBooks as $book)
                        <option value="{{ $book->book_number }}">{{ $book->name }}</option>
                    @endforeach
                </select>
            </div>

            <template x-if="loading">
                <div class="space-y-3 py-2">
                    <div class="animate-pulse h-2 bg-slate-100 dark:bg-slate-800 rounded w-3/4"></div>
                    <div class="animate-pulse h-2 bg-slate-100 dark:bg-slate-800 rounded w-1/2"></div>
                    <div class="animate-pulse h-2 bg-slate-100 dark:bg-slate-800 rounded w-full"></div>
                </div>
            </template>

            <template x-if="!loading && panorama">
                <div class="space-y-4 text-sm">
                    <div class="group">
                        <p class="text-[10px] font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-1">Autor</p>
                        <p class="text-slate-700 dark:text-slate-300 font-medium leading-relaxed" x-text="panorama.author || '—'"></p>
                    </div>
                    <div class="group">
                        <p class="text-[10px] font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-1">Época / Data</p>
                        <p class="text-slate-700 dark:text-slate-300 font-medium leading-relaxed" x-text="panorama.date_written || '—'"></p>
                    </div>
                    <div class="group">
                        <p class="text-[10px] font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-1">Mensagem Central</p>
                        <p class="text-slate-700 dark:text-slate-300 font-medium leading-relaxed bg-slate-50 dark:bg-slate-950/20 p-3 rounded-xl border border-slate-100 dark:border-slate-800" x-text="panorama.theme_central || '—'"></p>
                    </div>
                    <div class="group">
                        <p class="text-[10px] font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-1">Audiência Original</p>
                        <p class="text-slate-700 dark:text-slate-300 font-medium leading-relaxed" x-text="panorama.recipients || '—'"></p>
                    </div>
                </div>
            </template>

            <template x-if="!loading && selectedBookNumber && !panorama">
                <div class="flex flex-col items-center justify-center py-4 text-center">
                    <i class="fa-pro fa-solid fa-file-magnifying-glass text-slate-200 dark:text-slate-700 text-2xl mb-2"></i>
                    <p class="text-xs text-slate-400 font-medium">Draft de panorama indisponível.</p>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function contextoBiblico() {
        return {
            open: true,
            selectedBookNumber: '',
            panorama: null,
            loading: false,
            async fetchPanorama() {
                if (!this.selectedBookNumber) { this.panorama = null; return; }
                this.loading = true;
                this.panorama = null;
                try {
                    const res = await fetch('/api/v1/bible/panorama?book_number=' + encodeURIComponent(this.selectedBookNumber));
                    const json = await res.json();
                    if (res.ok && json.data) this.panorama = json.data;
                } catch (e) {}
                this.loading = false;
            }
        };
    }
</script>
