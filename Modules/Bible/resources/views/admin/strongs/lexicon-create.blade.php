@extends('admin::components.layouts.master')

@section('title', 'Criar Strong Lexicon')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Nova entrada no Strong Lexicon</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Cadastre/defina metadados do termo original e sua descrição.</p>
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
                    <x-icon name="plus" style="duotone" class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" />
                    Dados da entrada
                </h2>
            </div>

            <form action="{{ route('admin.bible.strongs-lexicon.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Número Strong <span class="text-red-500">*</span></label>
                        <input type="text" name="number" required maxlength="10"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors"
                               placeholder="Ex: G2316">
                        @error('number') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Idioma <span class="text-red-500">*</span></label>
                        <select name="lang" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors">
                            <option value="he">hebraico</option>
                            <option value="gr">grego</option>
                        </select>
                        @error('lang') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Lemma</label>
                        <input type="text" name="lemma"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors">
                        @error('lemma') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pronúncia</label>
                        <input type="text" name="pronounce"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors">
                        @error('pronounce') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">XLit</label>
                        <input type="text" name="xlit"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors">
                        @error('xlit') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-end">
                        <div class="w-full">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Revisado?</label>
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="is_reviewed" value="1"
                                       class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Marcar como revisado</span>
                            </div>
                            @error('is_reviewed') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Descrição PT</label>
                    <textarea name="description_pt" rows="6"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors resize-none"
                              placeholder="Definição completa PT-BR (descrição ampliada)"></textarea>
                    @error('description_pt') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Equivalente Semântico (PT)</label>
                    <textarea name="lemma_br" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors resize-none"
                              placeholder="Semântica PT (curta/média)"></textarea>
                    @error('lemma_br') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.bible.strongs-lexicon.index') }}"
                       class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 text-sm font-medium text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                        Criar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

