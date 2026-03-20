@extends('memberpanel::components.layouts.master')

@section('title', 'Editar Sermão Expositivo')

@section('content')
<div x-data="sermonBuilder()" class="mx-auto max-w-7xl px-3 py-4 sm:px-6">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Editor de Sermão</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400">Atualize conteúdo, referências e aplicação pastoral mantendo padrão editorial consistente.</p>
        </div>
        <a href="{{ route('memberpanel.sermons.show', $sermon) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">Voltar</a>
    </div>

    <form action="{{ route('memberpanel.sermons.update', $sermon) }}" method="POST" class="grid grid-cols-1 gap-4 lg:grid-cols-5">
        @csrf
        @method('PUT')

        <section class="space-y-4 lg:col-span-3">
            @if($errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 dark:border-rose-900/40 dark:bg-rose-900/20 dark:text-rose-300">
                    <p class="mb-2 font-bold">Ajuste os campos abaixo para continuar:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <input name="title" value="{{ old('title', $sermon->title) }}" required placeholder="Título do sermão" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    <input name="theme" value="{{ old('theme', $sermon->theme) }}" placeholder="Tema central" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    <input name="biblical_text_base" value="{{ old('biblical_text_base', $sermon->biblical_text_base) }}" required placeholder="Texto base" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 md:col-span-2">
                    <input name="subtitle" value="{{ old('subtitle', $sermon->subtitle) }}" placeholder="Subtítulo" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 md:col-span-2">
                    <textarea name="description" rows="2" placeholder="Resumo pastoral" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 md:col-span-2">{{ old('description', $sermon->description) }}</textarea>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <label class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-200">Contexto histórico-gramatical</label>
                <x-rich-editor name="historical_context" :value="old('historical_context', $sermon->historical_context)" />
                <label class="mb-1 mt-4 block text-sm font-semibold text-slate-700 dark:text-slate-200">Proposição central</label>
                <x-rich-editor name="central_proposition" :value="old('central_proposition', $sermon->central_proposition)" />
                <label class="mb-1 mt-4 block text-sm font-semibold text-slate-700 dark:text-slate-200">Introdução</label>
                <x-rich-editor name="introduction" :value="old('introduction', $sermon->introduction)" />
                <label class="mb-1 mt-4 block text-sm font-semibold text-slate-700 dark:text-slate-200">Corpo do esboço</label>
                <x-rich-editor name="body_outline" :value="old('body_outline', $sermon->body_outline)" />
                <label class="mb-1 mt-4 block text-sm font-semibold text-slate-700 dark:text-slate-200">Aplicação prática</label>
                <x-rich-editor name="practical_application" :value="old('practical_application', $sermon->practical_application)" />
                <label class="mb-1 mt-4 block text-sm font-semibold text-slate-700 dark:text-slate-200">Conclusão</label>
                <x-rich-editor name="conclusion" :value="old('conclusion', $sermon->conclusion)" />
                <label class="mb-1 mt-4 block text-sm font-semibold text-slate-700 dark:text-slate-200">Manuscrito completo</label>
                <x-rich-editor name="full_content" :value="old('full_content', $sermon->full_content)" />
            </div>

            @include('sermons::components.bible-reference-picker', ['versions' => $bibleVersions, 'references' => old('bible_references', $sermon->bibleReferences->toArray())])

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="grid grid-cols-2 gap-3">
                    <select name="category_id" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                        <option value="">Categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $sermon->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select name="sermon_series_id" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                        <option value="">Série expositiva</option>
                        @foreach($series as $serie)
                            <option value="{{ $serie->id }}" @selected(old('sermon_series_id', $sermon->sermon_series_id) == $serie->id)>{{ $serie->title }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                        <option value="draft" @selected(old('status', $sermon->status) === 'draft')>Rascunho</option>
                        <option value="published" @selected(old('status', $sermon->status) === 'published')>Publicado</option>
                    </select>
                    <select name="visibility" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                        <option value="members" @selected(old('visibility', $sermon->visibility) === 'members')>Membros</option>
                        <option value="public" @selected(old('visibility', $sermon->visibility) === 'public')>Público</option>
                        <option value="private" @selected(old('visibility', $sermon->visibility) === 'private')>Privado</option>
                    </select>
                    <input type="date" name="sermon_date" value="{{ old('sermon_date', optional($sermon->sermon_date)->format('Y-m-d')) }}" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    <select name="sermon_structure_type" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                        <option value="">Estrutura homilética</option>
                        <option value="expositivo" @selected(old('sermon_structure_type', $sermon->sermon_structure_type) === 'expositivo')>Expositivo</option>
                        <option value="temático" @selected(old('sermon_structure_type', $sermon->sermon_structure_type) === 'temático')>Temático</option>
                        <option value="textual" @selected(old('sermon_structure_type', $sermon->sermon_structure_type) === 'textual')>Textual</option>
                    </select>
                </div>
                <label class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                    <input type="checkbox" name="is_collaborative" value="1" @checked(old('is_collaborative', $sermon->is_collaborative)) class="rounded border-slate-300 text-amber-600 focus:ring-amber-600">
                    Permitir colaboração
                </label>
                <div class="mt-4">
                    <p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tags do Sermão</p>
                    <div class="grid grid-cols-2 gap-2 md:grid-cols-3">
                        @php($selectedTags = collect(old('tags', $sermon->tags->pluck('id')->all())))
                        @foreach($tags as $tag)
                            <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked($selectedTags->contains($tag->id)) class="rounded border-slate-300 text-amber-600 focus:ring-amber-600">
                                {{ $tag->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                @if($sermon->canDelete(auth()->user()))
                    <button type="button" onclick="if(confirm('Excluir este sermão?')) document.getElementById('delete-form').submit();" class="rounded-xl border border-red-300 px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:border-red-700 dark:text-red-400 dark:hover:bg-red-900/20">Excluir</button>
                @endif
                <button class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700 dark:bg-slate-100 dark:text-slate-900">
                    Salvar alterações
                </button>
            </div>
        </section>

        <aside class="lg:col-span-2">
            <div class="sticky top-4 space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Painel bíblico de apoio</h3>
                <input x-model="bibleQuery" @keydown.enter.prevent="searchBible()" placeholder="Buscar texto bíblico (ex: Rm 8:1-11)" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                <button type="button" @click="searchBible()" class="w-full rounded-lg bg-slate-800 px-3 py-2 text-sm text-white hover:bg-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 dark:bg-slate-200 dark:text-slate-900">Pesquisar referência</button>
                <input x-model="strongQuery" @keydown.enter.prevent="searchStrong()" placeholder="Strong (ex: H430)" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                <button type="button" @click="searchStrong()" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800">Consultar léxico Strong</button>
                <div class="max-h-[60vh] space-y-2 overflow-y-auto">
                    <template x-if="bibleResult">
                        <div class="rounded-xl border border-slate-200 bg-white p-3 text-sm dark:border-slate-700 dark:bg-slate-800">
                            <p class="font-semibold text-slate-900 dark:text-slate-100" x-text="bibleResult.reference || 'Resultado bíblico'"></p>
                            <p class="mt-1 text-slate-700 dark:text-slate-300" x-text="bibleResult.text"></p>
                        </div>
                    </template>
                    <template x-if="strongResult">
                        <div class="rounded-xl border border-slate-200 bg-white p-3 text-sm dark:border-slate-700 dark:bg-slate-800">
                            <p class="font-semibold text-slate-900 dark:text-slate-100" x-text="strongResult.number"></p>
                            <p class="text-slate-600 dark:text-slate-300" x-text="strongResult.lemma"></p>
                            <p class="mt-1 text-slate-700 dark:text-slate-300" x-text="strongResult.description_pt"></p>
                        </div>
                    </template>
                </div>
            </div>
        </aside>
    </form>

    @if($sermon->canDelete(auth()->user()))
        <form id="delete-form" action="{{ route('memberpanel.sermons.destroy', $sermon) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>

<script>
function sermonBuilder() {
    return {
        bibleQuery: '',
        strongQuery: '',
        bibleResult: null,
        strongResult: null,
        references: [],
        async init() {
            this.references = (@json(old('bible_references', $sermon->bibleReferences->toArray())) || []).map(this.decorateReference);
            for (let i = 0; i < this.references.length; i++) {
                const ref = this.references[i];
                if (ref.bible_version_id) await this.loadBooks(i);
                if (ref.book_id) await this.loadChapters(i);
                if (ref.chapter_id) await this.loadVerses(i);
            }
        },
        decorateReference(reference = {}) {
            return { uid: crypto.randomUUID(), bible_version_id: reference.bible_version_id || '', type: reference.type || 'main', book_id: reference.book_id || '', chapter_id: reference.chapter_id || '', verse_start_id: reference.verse_start_id || '', verse_end_id: reference.verse_end_id || '', context: reference.context || '', books: [], chapters: [], verses: [], loadingBooks: false, loadingChapters: false, loadingVerses: false };
        },
        async searchBible() {
            if (!this.bibleQuery) return;
            const response = await fetch(`/api/v1/bible/search?q=${encodeURIComponent(this.bibleQuery)}`, { headers: { Accept: 'application/json' } });
            const payload = await response.json();
            if (!response.ok) return;
            if (Array.isArray(payload.data)) {
                const first = payload.data[0];
                this.bibleResult = first ? { reference: first.reference, text: first.text } : null;
            } else if (payload.data?.verses) {
                this.bibleResult = { reference: payload.data.reference, text: payload.data.verses.map(v => v.text).join(' ') };
            }
        },
        async searchStrong() {
            if (!this.strongQuery) return;
            const response = await fetch(`/api/v1/bible/strong/${encodeURIComponent(this.strongQuery)}`, { headers: { Accept: 'application/json' } });
            const payload = await response.json();
            if (response.ok) this.strongResult = payload.data;
        },
        addReference() { this.references.push(this.decorateReference()); },
        removeReference(index) { this.references.splice(index, 1); },
        async loadBooks(index) {
            const ref = this.references[index];
            ref.loadingBooks = true;
            const response = await fetch(`/api/v1/bible/books?version_id=${ref.bible_version_id}`, { headers: { Accept: 'application/json' } });
            const payload = await response.json();
            ref.books = payload.data || [];
            ref.loadingBooks = false;
        },
        async loadChapters(index) {
            const ref = this.references[index];
            ref.loadingChapters = true;
            const response = await fetch(`/api/v1/bible/chapters?book_id=${ref.book_id}`, { headers: { Accept: 'application/json' } });
            const payload = await response.json();
            ref.chapters = payload.data || [];
            ref.loadingChapters = false;
        },
        async loadVerses(index) {
            const ref = this.references[index];
            ref.loadingVerses = true;
            const response = await fetch(`/api/v1/bible/verses?chapter_id=${ref.chapter_id}`, { headers: { Accept: 'application/json' } });
            const payload = await response.json();
            ref.verses = payload.data || [];
            ref.loadingVerses = false;
        },
    };
}
</script>
@endsection
