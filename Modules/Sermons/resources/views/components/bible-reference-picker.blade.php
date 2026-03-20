@props([
    'versions' => collect(),
    'references' => [],
])

<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="mb-3 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Passagens vinculadas</h3>
        <button
            type="button"
            @click="addReference()"
            class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700"
        >
            Adicionar
        </button>
    </div>

    <template x-if="references.length === 0">
        <p class="text-xs text-slate-500 dark:text-slate-400">Nenhuma passagem vinculada ainda.</p>
    </template>

    <div class="space-y-3">
        <template x-for="(reference, index) in references" :key="reference.uid">
            <div class="rounded-xl border border-slate-200 p-3 dark:border-slate-600">
                <div class="mb-2 grid grid-cols-2 gap-2">
                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Versão</label>
                        <select :name="`bible_references[${index}][bible_version_id]`" x-model="reference.bible_version_id" @change="loadBooks(index)" class="w-full rounded-lg border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                            <option value="">Selecione</option>
                            @foreach($versions as $version)
                                <option value="{{ $version->id }}">{{ $version->abbreviation ?? $version->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Tipo</label>
                        <select :name="`bible_references[${index}][type]`" x-model="reference.type" class="w-full rounded-lg border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                            <option value="main">Principal</option>
                            <option value="support">Apoio</option>
                            <option value="illustration">Ilustração</option>
                            <option value="other">Outro</option>
                        </select>
                    </div>
                </div>

                <div class="mb-2 grid grid-cols-2 gap-2">
                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Livro</label>
                        <select :name="`bible_references[${index}][book_id]`" x-model="reference.book_id" @change="loadChapters(index)" class="w-full rounded-lg border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                            <option value="">Selecione</option>
                            <template x-for="book in reference.books" :key="book.id">
                                <option :value="book.id" x-text="book.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Capítulo</label>
                        <select :name="`bible_references[${index}][chapter_id]`" x-model="reference.chapter_id" @change="loadVerses(index)" class="w-full rounded-lg border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                            <option value="">Selecione</option>
                            <template x-for="chapter in reference.chapters" :key="chapter.id">
                                <option :value="chapter.id" x-text="chapter.chapter_number"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="mb-2 grid grid-cols-2 gap-2">
                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Verso inicial</label>
                        <select :name="`bible_references[${index}][verse_start_id]`" x-model="reference.verse_start_id" class="w-full rounded-lg border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                            <option value="">Selecione</option>
                            <template x-for="verse in reference.verses" :key="verse.id">
                                <option :value="verse.id" x-text="verse.verse_number"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-600 dark:text-slate-300">Verso final</label>
                        <select :name="`bible_references[${index}][verse_end_id]`" x-model="reference.verse_end_id" class="w-full rounded-lg border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                            <option value="">Mesmo do inicial</option>
                            <template x-for="verse in reference.verses" :key="`end-${verse.id}`">
                                <option :value="verse.id" x-text="verse.verse_number"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <textarea :name="`bible_references[${index}][context]`" x-model="reference.context" rows="2" class="mb-2 w-full rounded-lg border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" placeholder="Observação exegética"></textarea>

                <button type="button" @click="removeReference(index)" class="text-xs font-medium text-red-600 hover:text-red-700 dark:text-red-400">
                    Remover
                </button>
            </div>
        </template>
    </div>
</div>

