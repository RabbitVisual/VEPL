@extends('admin::components.layouts.master')

@section('title', 'Editar Strong '.$entry->number)

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Editar Strong {{ $entry->number }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Atualize metadados do termo original e definições.</p>
            </div>
            <a href="{{ route('admin.bible.strongs-lexicon.index') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" />
                Voltar
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 bg-linear-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <x-icon name="pen-to-square" style="duotone" class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" />
                    Dados do Lexicon
                </h2>
            </div>

            <form action="{{ route('admin.bible.strongs-lexicon.update', $entry->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Número Strong</label>
                        <input type="text" value="{{ $entry->number }}" disabled
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300 transition-colors">
                        <input type="hidden" name="number" value="{{ $entry->number }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Idioma <span class="text-red-500">*</span></label>
                        <select name="lang" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-amber-600 dark:bg-gray-700 dark:text-white transition-colors">
                            <option value="he" {{ $entry->lang === 'he' ? 'selected' : '' }}>hebraico</option>
                            <option value="gr" {{ $entry->lang === 'gr' ? 'selected' : '' }}>grego</option>
                        </select>
                        @error('lang') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Lemma</label>
                        <input type="text" name="lemma" value="{{ old('lemma', $entry->lemma) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-amber-600 dark:bg-gray-700 dark:text-white transition-colors">
                        @error('lemma') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pronúncia</label>
                        <input type="text" name="pronounce" value="{{ old('pronounce', $entry->pronounce) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-amber-600 dark:bg-gray-700 dark:text-white transition-colors">
                        @error('pronounce') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">XLit</label>
                        <input type="text" name="xlit" value="{{ old('xlit', $entry->xlit) }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-amber-600 dark:bg-gray-700 dark:text-white transition-colors">
                        @error('xlit') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-end">
                        <div class="w-full">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Revisado?</label>
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="is_reviewed" value="1" {{ old('is_reviewed', $entry->is_reviewed) ? 'checked' : '' }}
                                       class="w-5 h-5 text-amber-600 bg-gray-100 border-gray-300 rounded focus:ring-amber-600 dark:focus:ring-amber-600 dark:ring-offset-gray-800 focus:ring-2">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Marcar como revisado</span>
                            </div>
                            @error('is_reviewed') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Descrição PT</label>
                    <textarea name="description_pt" rows="6"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-amber-600 dark:bg-gray-700 dark:text-white transition-colors resize-none"
                              placeholder="Definição completa PT-BR (descrição ampliada)">{{ old('description_pt', $entry->description_pt) }}</textarea>
                    @error('description_pt') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Equivalente Semântico (PT)</label>
                    <textarea name="lemma_br" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-amber-600 dark:bg-gray-700 dark:text-white transition-colors resize-none"
                              placeholder="Semântica PT (curta/média)">{{ old('lemma_br', $entry->lemma_br) }}</textarea>
                    @error('lemma_br') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.bible.strongs-lexicon.show', $entry->id) }}"
                       class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

