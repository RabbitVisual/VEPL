@extends('admin::components.layouts.master')

@section('title', 'Editar Tag Interlinear #'.$entry->id)

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Editar Tag Interlinear #{{ $entry->id }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Atualize metadados do interlinear.</p>
            </div>
            <a href="{{ route('admin.bible.word-tags.index') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" />
                Voltar
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 bg-linear-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <x-icon name="pen-to-square" style="duotone" class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" />
                    Edição
                </h2>
            </div>

            <form action="{{ route('admin.bible.word-tags.update', $entry->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">ID do Versículo <span class="text-red-500">*</span></label>
                        <input type="number" name="verse_id" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-amber-600 dark:bg-gray-700 dark:text-white transition-colors"
                               value="{{ old('verse_id', $entry->verse_id) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Posição <span class="text-red-500">*</span></label>
                        <input type="number" min="0" name="position" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-amber-600 dark:bg-gray-700 dark:text-white transition-colors"
                               value="{{ old('position', $entry->position) }}">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Palavra Original <span class="text-red-500">*</span></label>
                    <input type="text" name="word_surface" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-amber-600 dark:bg-gray-700 dark:text-white transition-colors"
                           value="{{ old('word_surface', $entry->word_surface) }}">
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Número Strong</label>
                        <input type="text" name="strong_number"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-amber-600 dark:bg-gray-700 dark:text-white transition-colors"
                               value="{{ old('strong_number', $entry->strong_number) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Idioma <span class="text-red-500">*</span></label>
                        <select name="lang" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-amber-600 dark:bg-gray-700 dark:text-white transition-colors">
                            <option value="he" {{ old('lang', $entry->lang)==='he'?'selected':'' }}>Hebraico</option>
                            <option value="gr" {{ old('lang', $entry->lang)==='gr'?'selected':'' }}>Grego</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Morfologia</label>
                    <input type="text" name="morphology"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-600 focus:border-amber-600 dark:bg-gray-700 dark:text-white transition-colors"
                           value="{{ old('morphology', $entry->morphology) }}">
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.bible.word-tags.show', $entry->id) }}"
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

