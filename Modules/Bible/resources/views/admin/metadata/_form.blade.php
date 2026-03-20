@php
    $formAction = $item
        ? route('admin.bible.metadata.update', $item)
        : route('admin.bible.metadata.store');
@endphp

<form action="{{ $formAction }}" method="POST" class="space-y-6" x-data="metadataReferenceSelector()">
    @csrf
    @if($item)
        @method('PUT')
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Versão</label>
                <select name="bible_version_id" x-model="versionId" @change="onVersionChange" class="w-full rounded-xl border-slate-300 text-slate-900 focus:border-amber-600 focus:ring-amber-600">
                    <option value="">Selecione</option>
                    @foreach($versions as $version)
                        <option value="{{ $version->id }}" @selected($selectedVersionId === $version->id)>
                            {{ $version->abbreviation }} - {{ $version->name }}
                        </option>
                    @endforeach
                </select>
                @error('bible_version_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Livro</label>
                <select name="book_id" x-model="bookId" @change="onBookChange" class="w-full rounded-xl border-slate-300 text-slate-900 focus:border-amber-600 focus:ring-amber-600">
                    <option value="">Selecione</option>
                    <template x-for="book in books" :key="book.id">
                        <option :value="book.id" x-text="`${book.book_number} - ${book.name}`"></option>
                    </template>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}" x-show="false" @selected($selectedBookId === $book->id)>
                            {{ $book->book_number }} - {{ $book->name }}
                        </option>
                    @endforeach
                </select>
                @error('book_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Capítulo</label>
                <select name="chapter_id" x-model="chapterId" @change="onChapterChange" class="w-full rounded-xl border-slate-300 text-slate-900 focus:border-amber-600 focus:ring-amber-600">
                    <option value="">Selecione</option>
                    <template x-for="chapter in chapters" :key="chapter.id">
                        <option :value="chapter.id" x-text="chapter.chapter_number"></option>
                    </template>
                    @foreach($chapters as $chapter)
                        <option value="{{ $chapter->id }}" x-show="false" @selected($selectedChapterId === $chapter->id)>
                            {{ $chapter->chapter_number }}
                        </option>
                    @endforeach
                </select>
                @error('chapter_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Versículo</label>
                <select name="verse_id" x-model="verseId" class="w-full rounded-xl border-slate-300 text-slate-900 focus:border-amber-600 focus:ring-amber-600">
                    <option value="">Selecione</option>
                    <template x-for="verse in verses" :key="verse.id">
                        <option :value="verse.id" x-text="verse.verse_number"></option>
                    </template>
                    @foreach($verses as $verse)
                        <option value="{{ $verse->id }}" x-show="false" @selected($selectedVerseId === $verse->id)>
                            {{ $verse->verse_number }}
                        </option>
                    @endforeach
                </select>
                @error('verse_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Comentário Oficial</h2>
            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                <input type="checkbox" name="is_published" value="1" class="rounded border-slate-300 text-amber-600 focus:ring-amber-600" @checked(old('is_published', $item?->is_published ?? true))>
                Publicado
            </label>
        </div>

        <x-rich-editor
            name="official_commentary"
            :value="old('official_commentary', $item?->official_commentary)"
            placeholder="Escreva aqui o comentário oficial da VEPL para esta referência."
        />
        @error('official_commentary')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700">
            Salvar comentário oficial
        </button>
        <a href="{{ route('admin.bible.metadata.index') }}" class="inline-flex items-center rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
            Voltar
        </a>
    </div>
</form>

@once
    @push('scripts')
        <script>
            function metadataReferenceSelector() {
                return {
                    versionId: @json((string) $selectedVersionId),
                    bookId: @json((string) $selectedBookId),
                    chapterId: @json((string) $selectedChapterId),
                    verseId: @json((string) $selectedVerseId),
                    books: @json($books->values()),
                    chapters: @json($chapters->values()),
                    verses: @json($verses->values()),

                    async onVersionChange() {
                        this.bookId = '';
                        this.chapterId = '';
                        this.verseId = '';
                        this.chapters = [];
                        this.verses = [];
                        if (!this.versionId) {
                            this.books = [];
                            return;
                        }
                        const res = await fetch(`{{ route('admin.bible.metadata.options.books') }}?bible_version_id=${encodeURIComponent(this.versionId)}`);
                        const payload = await res.json();
                        this.books = payload.data ?? [];
                    },

                    async onBookChange() {
                        this.chapterId = '';
                        this.verseId = '';
                        this.verses = [];
                        if (!this.bookId) {
                            this.chapters = [];
                            return;
                        }
                        const res = await fetch(`{{ route('admin.bible.metadata.options.chapters') }}?book_id=${encodeURIComponent(this.bookId)}`);
                        const payload = await res.json();
                        this.chapters = payload.data ?? [];
                    },

                    async onChapterChange() {
                        this.verseId = '';
                        if (!this.chapterId) {
                            this.verses = [];
                            return;
                        }
                        const res = await fetch(`{{ route('admin.bible.metadata.options.verses') }}?chapter_id=${encodeURIComponent(this.chapterId)}`);
                        const payload = await res.json();
                        this.verses = payload.data ?? [];
                    },
                };
            }
        </script>
    @endpush
@endonce
