<div x-data="biblePicker()"
     x-show="isOpen"
     @open-bible-picker.window="open()"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none;"
     class="fixed inset-0 z-[60] flex items-center justify-center p-4">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity" @click="close()"></div>

    <!-- Modal -->
    <div class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden transform transition-all scale-100"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        <!-- Header -->
        <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-950/20">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                    <x-icon name="book-sparkles" style="solid" class="text-blue-500" />
                    Citar Manuscrito Sagrado
                </h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Busca Direta na API de Originais</p>
            </div>
            <button @click="close()" class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                <x-icon name="xmark" style="solid" class="text-lg" />
            </button>
        </div>

        <!-- Body -->
        <div class="p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Livro Bíblico</label>
                    <select x-model="selectedBook" @change="resetChapter()"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all font-bold">
                        <option value="">Selecione o Livro...</option>
                        @foreach($bibleBooks as $book)
                            <option value="{{ $book->id }}" data-name="{{ $book->name }}">{{ $book->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Capítulo</label>
                        <input type="number" x-model="chapter" min="1"
                            class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all font-bold text-center">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Versículo(s)</label>
                        <input type="text" x-model="verses" placeholder="Ex: 1-5"
                            class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-900 dark:text-white focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all font-bold text-center">
                    </div>
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-3">
                    <label class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Texto Revelado</label>
                    <button type="button" @click="fetchText()" :disabled="fetching"
                        class="px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[10px] font-extrabold uppercase tracking-widest hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-all disabled:opacity-50 flex items-center gap-2">
                        <template x-if="fetching">
                            <span class="flex items-center gap-2"><x-icon name="spinner-third" style="solid" class="animate-spin" /> Consultando...</span>
                        </template>
                        <template x-if="!fetching">
                            <span class="flex items-center gap-2"><x-icon name="cloud-arrow-down" style="solid" /> Sincronizar Texto</span>
                        </template>
                    </button>
                </div>
                <p x-show="fetchError" class="text-[11px] font-bold text-red-500 mb-2 flex items-center gap-1.5" x-text="fetchError"></p>
                <textarea x-model="text" rows="5"
                    class="w-full rounded-2xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/30 text-slate-700 dark:text-slate-300 focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all font-serif italic p-4 leading-relaxed"
                    placeholder="O texto sagrado aparecerá aqui para sua revisão antes da inserção..."></textarea>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-950/20 flex justify-end gap-4 border-t border-slate-100 dark:border-slate-800">
            <button @click="close()" class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                Cancelar
            </button>
            <button @click="insert()"
                class="px-8 py-3 text-sm font-extrabold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg shadow-blue-500/20 transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="!text || !selectedBook">
                <x-icon name="file-import" style="solid" class="mr-2" />
                Inserir Citação
            </button>
        </div>
    </div>
</div>

<script>
    function biblePicker() {
        return {
            isOpen: false,
            selectedBook: '',
            chapter: '',
            verses: '',
            text: '',
            fetching: false,
            fetchError: '',

            open() {
                this.isOpen = true;
                this.fetchError = '';
            },

            close() {
                this.isOpen = false;
                this.reset();
                this.fetchError = '';
            },

            reset() {
                this.text = '';
            },

            resetChapter() {
                this.chapter = '';
                this.verses = '';
                this.text = '';
                this.fetchError = '';
            },

            async fetchText() {
                if (!this.selectedBook || !this.chapter) return;
                this.fetching = true;
                this.fetchError = '';
                const params = new URLSearchParams({
                    book_id: this.selectedBook,
                    chapter_number: this.chapter
                });
                if (this.verses) params.set('verse_range', this.verses);
                try {
                    const res = await fetch('/api/v1/bible/verses?' + params.toString());
                    const json = await res.json();
                    if (!res.ok) {
                        this.fetchError = json.message || 'Não foi possível buscar os versículos.';
                        this.text = '';
                        return;
                    }
                    const data = json.data || [];
                    this.text = Array.isArray(data) ? data.map(v => v.text || '').filter(Boolean).join(' ') : '';
                    if (!this.text) this.fetchError = 'Nenhum versículo encontrado.';
                } catch (e) {
                    this.fetchError = 'Erro ao conectar com a API. Tente novamente.';
                    this.text = '';
                } finally {
                    this.fetching = false;
                }
            },

            insert() {
                const bookName = document.querySelector(`option[value="${this.selectedBook}"]`)?.innerText;
                const reference = `${bookName} ${this.chapter}:${this.verses}`;

                // Dispatch event to Rich Editor
                window.dispatchEvent(new CustomEvent('insert-bible-text', {
                    detail: {
                        text: this.text,
                        reference: reference
                    }
                }));

                this.close();
            }
        }
    }
</script>
