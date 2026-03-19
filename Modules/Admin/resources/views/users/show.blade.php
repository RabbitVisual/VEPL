@extends('admin::components.layouts.master')

@section('title', 'Perfil: ' . $user->name)

@section('content')
<div class="space-y-8" x-data="{ tab: 'general' }">
    <!-- Immersive Header -->
    <div class="relative rounded-3xl overflow-hidden bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700">
        <!-- Badges strip -->
        <div class="absolute top-4 left-8 z-10 flex flex-wrap items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-white text-[10px] font-bold uppercase tracking-widest">Membros</span>
            <span class="px-3 py-1 rounded-full {{ $user->is_active ? 'bg-green-500/30 border-green-400/50 text-green-100' : 'bg-red-500/30 border-red-400/50 text-red-100' }} border text-[10px] font-bold uppercase tracking-widest">{{ $user->is_active ? 'Ativo' : 'Inativo' }}</span>
        </div>
        <!-- Banner Background -->
        <div class="h-48 bg-gradient-to-br from-blue-700 via-indigo-700 to-purple-800 relative overflow-hidden">
            <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: url('{{ asset('storage/image/pattern.png') }}'); background-size: 100px;"></div>
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl"></div>
        </div>

        <!-- Profile Info Overlay -->
        <div class="px-8 pb-8 flex flex-col md:flex-row gap-8 items-end -mt-16 relative">
            <!-- avatar -->
            <div class="relative group">
                <div class="w-40 h-40 rounded-3xl border-8 border-white dark:border-gray-800 shadow-2xl overflow-hidden bg-gray-100 dark:bg-gray-700">
                    @if ($user->photo)
                        <img src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 bg-linear-to-tr from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600">
                            <x-icon name="user" class="w-20 h-20" />
                        </div>
                    @endif
                </div>
                <div class="absolute -bottom-2 -right-2 w-10 h-10 rounded-xl bg-green-500 border-4 border-white dark:border-gray-800 flex items-center justify-center text-white" title="Status: {{ $user->is_active ? 'Ativo' : 'Inativo' }}">
                    <x-icon name="{{ $user->is_active ? 'check' : 'xmark' }}" class="w-5 h-5" />
                </div>
            </div>

            <!-- Basic Info -->
            <div class="flex-1 space-y-3 pb-2">
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white tracking-tight">{{ $user->name }}</h1>
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                        {{ $user->role->name }}
                    </span>
                </div>

                <div class="flex flex-wrap gap-6 text-sm">
                    <div class="flex items-center text-gray-500 dark:text-gray-400 font-medium">
                        <x-icon name="envelope" class="w-4 h-4 mr-2" />
                        {{ $user->email }}
                    </div>
                    @if($user->cellphone)
                    <div class="flex items-center text-gray-500 dark:text-gray-400 font-medium">
                        <x-icon name="phone" class="w-4 h-4 mr-2" />
                        {{ $user->cellphone }}
                    </div>
                    @endif
                    <div class="flex items-center text-gray-500 dark:text-gray-400 font-medium">
                        <x-icon name="user-tie" class="w-4 h-4 mr-2" />
                        {{ $user->title ?: 'Líder / Seminarista' }}
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex gap-3 mb-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="flex items-center px-6 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 transition-all border border-gray-200 dark:border-gray-600 shadow-sm active:scale-95">
                    <x-icon name="pencil" class="w-4 h-4 mr-2" />
                    Editar
                </a>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="px-8 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30">
            <div class="flex gap-8">
                <button @click="tab = 'general'" :class="tab === 'general' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-4 border-b-2 font-black text-[10px] uppercase tracking-widest transition-all">Geral</button>
                <button @click="tab = 'church'" :class="tab === 'church' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-4 border-b-2 font-black text-[10px] uppercase tracking-widest transition-all">Vida Eclesiástica</button>
                <button @click="tab = 'ministries'" :class="tab === 'ministries' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-4 border-b-2 font-black text-[10px] uppercase tracking-widest transition-all">Ministérios</button>
                <button @click="tab = 'family'" :class="tab === 'family' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-4 border-b-2 font-black text-[10px] uppercase tracking-widest transition-all">Família & Relacionamentos</button>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Sidebar: Stats & Gamification -->
        <div class="lg:col-span-4 space-y-8">


            <!-- Completion Card -->
            @php $completion = $user->getProfileCompletionPercentage(); @endphp
            <div class="bg-linear-to-br from-indigo-900 to-blue-900 rounded-3xl p-8 text-white shadow-xl shadow-indigo-900/20">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-indigo-300 mb-6">Perfil do Membro</h3>
                <div class="text-4xl font-black mb-2">{{ $completion }}%</div>
                <p class="text-sm font-medium text-indigo-200 mb-6">Campos obrigatórios preenchidos corretamente no sistema.</p>
                <div class="h-2 w-full bg-indigo-950/50 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-400 rounded-full" style="width: {{ $completion }}%"></div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 min-h-[600px] overflow-hidden">

                <!-- Tab: General Info -->
                <div x-show="tab === 'general'" x-cloak class="p-8 space-y-10 animate-fade-in">
                    <!-- Section: Personal -->
                    <section class="space-y-6">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                             <x-icon name="user" class="w-4 h-4" />
                             Dados Pessoais
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-blue-500 transition-colors">Nome Completo</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $user->name }}</p>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-blue-500 transition-colors">CPF</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $user->cpf ?? 'Não registrado' }}</p>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-blue-500 transition-colors">Data de Nascimento</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $user->date_of_birth?->format('d/m/Y') ?? 'Não registrado' }}</p>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-blue-500 transition-colors">Estado Civil</label>
                                <p class="text-gray-900 dark:text-white font-bold capitalize">{{ $user->marital_status ?? 'Não registrado' }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Section: Contact -->
                    <section class="space-y-6 pt-10 border-t border-gray-100 dark:border-gray-700">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                             <x-icon name="at-symbol" class="w-4 h-4" />
                             Contato e Localização
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-blue-500 transition-colors">Endereço Residencial</label>
                                <p class="text-gray-900 dark:text-white font-bold">
                                    {{ $user->address ?? 'Não registrado' }}@if($user->address_number), {{ $user->address_number }}@endif
                                </p>
                                <p class="text-sm text-gray-500 font-medium">
                                    {{ $user->neighborhood }} - {{ $user->city }}/{{ $user->state }}
                                </p>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-blue-500 transition-colors">Telefone Principal</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $user->cellphone ?? $user->phone ?? 'Não registrado' }}</p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Tab: Church Life (Ministerial Journey) -->
                <div x-show="tab === 'church'" x-cloak class="p-8 space-y-10 animate-fade-in">
                    <section class="space-y-8">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">Jornada Ministerial & Eclesiástica</h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-blue-500 transition-colors">Título Ministerial</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $user->title ?: 'Não informado' }}</p>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-blue-500 transition-colors">Formação Teológica</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $user->theological_education ?: 'Não informado' }}</p>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-blue-500 transition-colors">Status de Ordenação</label>
                                <p class="text-gray-900 dark:text-white font-bold">
                                    {{ $user->is_ordained ? 'Ordenado' : 'Não Ordenado (Aspirante/Líder)' }}
                                    @if($user->ordination_date)
                                        <span class="text-xs font-medium text-gray-500 ml-2">em {{ $user->ordination_date->format('d/m/Y') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-blue-500 transition-colors">Início no Ministério</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $user->ministry_start_date?->format('d/m/Y') ?: 'Não informado' }}</p>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-blue-500 transition-colors">Igreja Afiliada</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $user->affiliated_church ?: 'Igreja Batista Vertex' }}</p>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-blue-500 transition-colors">Convenção / Cooperação</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $user->baptist_convention ?: 'CBB - Convenção Batista Brasileira' }}</p>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-blue-500 transition-colors">Status de Batismo</label>
                                <p class="text-gray-900 dark:text-white font-bold">
                                    {{ $user->is_baptized ? 'Batizado' : 'Não Batizado/Em Preparo' }}
                                    @if($user->baptism_date)
                                        <span class="text-xs font-medium text-gray-500 ml-2">em {{ $user->baptism_date->format('d/m/Y') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($user->biography)
                        <div class="pt-8 border-t border-gray-100 dark:border-gray-700">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 group-hover:text-blue-500 transition-colors">Resumo Ministerial (Biografia)</label>
                            <div class="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-300 leading-relaxed italic">
                                "{{ $user->biography }}"
                            </div>
                        </div>
                        @endif
                    </section>
                </div>



                <!-- Tab: Family & Relationships -->
                <div x-show="tab === 'family'" x-cloak class="p-8 space-y-6 animate-fade-in" x-data="{ analysisOpen: false, analysisText: '', analysisLoading: false }">
                    <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-3 flex items-start gap-2 mb-4">
                        <x-icon name="information-circle" class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
                        <p class="text-xs text-amber-800 dark:text-amber-200">Vínculos pendentes podem ser aceitos pelo próprio membro no painel. Edite os vínculos nesta página para adicionar ou remover.</p>
                    </div>
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <x-icon name="people-group" class="w-4 h-4" />
                            Família & Relacionamentos
                        </h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($user->relationships as $rel)
                            <div class="group p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm transition-all hover:border-blue-500 overflow-hidden relative">
                                <div class="flex items-center gap-4">
                                    @if($rel->related_user_id && $rel->relatedUser)
                                        <a href="{{ route('admin.users.show', $rel->relatedUser) }}" class="shrink-0">
                                            @if($rel->relatedUser->photo)
                                                <img class="h-14 w-14 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600" src="{{ Storage::url($rel->relatedUser->photo) }}" alt="{{ $rel->relatedUser->name }}">
                                            @else
                                                <div class="h-14 w-14 rounded-full bg-linear-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-lg font-bold">
                                                    {{ strtoupper(substr($rel->relatedUser->first_name ?? $rel->relatedUser->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </a>
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('admin.users.show', $rel->relatedUser) }}" class="text-lg font-black text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors block truncate">{{ $rel->relatedUser->name }}</a>
                                            <div class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ $rel->relationship_type_label }}</div>
                                            <div class="mt-2 flex items-center gap-2 flex-wrap">
                                                @if($rel->status === 'accepted')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                                        <x-icon name="check" class="w-3 h-3" /> Confirmado
                                                    </span>
                                                @elseif($rel->status === 'pending')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">Pendente</span>
                                                    @if(auth()->user()->isAdmin() || (auth()->id() === $rel->related_user_id))
                                                        <form action="{{ route('admin.users.relationships.accept', $rel) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-[10px] font-bold text-green-600 dark:text-green-400 hover:underline">Aceitar</button>
                                                        </form>
                                                        <form action="{{ route('admin.users.relationships.reject', $rel) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-[10px] font-bold text-red-600 dark:text-red-400 hover:underline">Recusar</button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Recusado</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="h-14 w-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 shrink-0">
                                            <x-icon name="user" class="w-7 h-7" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-lg font-black text-gray-900 dark:text-white truncate">{{ $rel->related_name ?? '—' }}</div>
                                            <div class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ $rel->relationship_type_label }}</div>
                                            <span class="inline-flex items-center gap-1 mt-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Não membro</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 py-16 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-3xl">
                                <x-icon name="people-group" class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                                <p class="text-sm font-bold text-gray-500 mb-1">Nenhum vínculo familiar cadastrado.</p>
                                <p class="text-xs text-gray-400">Adicione parentes na edição do perfil.</p>
                                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-bold text-xs hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                    <x-icon name="plus" class="w-4 h-4" /> Editar vínculos
                                </a>
                            </div>
                        @endforelse
                    </div>

                </div>

                <!-- Tab: Ministries -->
                <div x-show="tab === 'ministries'" x-cloak class="p-8 space-y-6 animate-fade-in">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Atuação Ministerial</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($user->ministries as $ministry)
                            <div class="group p-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm transition-all hover:border-blue-500 overflow-hidden relative">
                                <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 dark:bg-blue-900/10 rounded-full group-hover:scale-150 transition-transform"></div>
                                <div class="relative">
                                    <div class="text-lg font-black text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 transition-colors uppercase tracking-tight">{{ $ministry->name }}</div>
                                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ $ministry->pivot->role ?? 'Membro' }}</div>
                                    <div class="mt-4 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ $ministry->pivot->status === 'active' ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $ministry->pivot->status === 'active' ? 'Ativo' : 'Pendente' }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 py-16 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-3xl">
                                <x-icon name="user-group" class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                                <p class="text-sm font-bold text-gray-500 mb-1">Membro não está em nenhum ministério.</p>
                                <p class="text-xs text-gray-400">Adicione-o à uma equipe na gestão de ministérios.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
