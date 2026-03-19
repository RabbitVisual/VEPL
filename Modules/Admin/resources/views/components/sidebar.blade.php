    $routes = [
        'bible' => request()->routeIs('admin.bible*'),
        'homepage' => request()->routeIs('admin.homepage*'),
        'events' => request()->routeIs('admin.events*') || request()->routeIs('admin.events.checkin*'),
        'sermons' => request()->routeIs('admin.sermons*'),
        'worship' => request()->routeIs('worship.admin.*'),
        'ministries' => request()->routeIs('admin.ministries*'),
    ];
@endphp

<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-40 w-72 bg-white dark:bg-slate-950 border-r border-gray-200 dark:border-gray-800 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-xl lg:shadow-none"
    x-data="{
        bibleOpen: {{ $routes['bible'] ? 'true' : 'false' }},
        homepageOpen: {{ $routes['homepage'] ? 'true' : 'false' }},
        eventsOpen: {{ $routes['events'] ? 'true' : 'false' }},
        sermonsOpen: {{ $routes['sermons'] ? 'true' : 'false' }},
        intercessorOpen: {{ request()->routeIs('admin.intercessor*') ? 'true' : 'false' }},
        worshipOpen: {{ $routes['worship'] ? 'true' : 'false' }},
        ministriesOpen: {{ $routes['ministries'] ? 'true' : 'false' }},
    }">

    <div class="flex flex-col h-full">
        <!-- Brand Logo -->
        <div class="flex items-center h-16 px-6 border-b border-gray-100 dark:border-gray-800 bg-white dark:bg-slate-950">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group w-full">
                <div class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-linear-to-tr from-blue-600 to-indigo-600 shadow-lg shadow-blue-500/20 group-hover:shadow-blue-500/40 transition-all duration-300 group-hover:scale-105">
                     <img src="{{ asset('storage/image/logo_icon.png') }}" alt="Logo" class="w-7 h-7 object-contain" >
                </div>
                <div class="flex flex-col">
                    <span class="text-base font-bold text-gray-900 dark:text-gray-100 tracking-tight leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Vertex CBAV</span>
                    <span class="text-[10px] font-medium text-gray-400 uppercase tracking-widest">Admin Panel</span>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">



            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.dashboard*')
                    ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="gauge-high" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.dashboard*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500 dark:group-hover:text-blue-400' }} transition-colors" />
                Dashboard
            </a>


            <div class="pt-4 pb-2">
                <p class="px-4 text-[11px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-wider">Sistema</p>
            </div>

            @if(auth()->user()->isAdmin())
                <!-- Modules -->
                <a href="{{ route('admin.modules.index') }}"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.modules*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <x-icon name="cubes" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.modules*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                    Módulos
                </a>

                <!-- CEP Ranges -->
                <a href="{{ route('admin.cep-ranges.index') }}"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.cep-ranges*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <x-icon name="map-location-dot" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.cep-ranges*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                    Gerenciar CEPs
                </a>

                <!-- Settings -->
                <a href="{{ route('admin.settings.index') }}"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.settings*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <x-icon name="gear" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.settings*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                    Configurações
                </a>
            @endif

            <div class="pt-4 pb-2">
                <p class="px-4 text-[11px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-wider">Gestão</p>
            </div>

            <!-- Users -->
            <a href="{{ route('admin.users.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.users*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="users" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.users*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Membros
            </a>

            @if(Route::has('admin.reports.family-demographics.index'))
            <a href="{{ route('admin.reports.family-demographics.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.reports.family-demographics*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="chart-pie" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.reports.family-demographics*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Inteligência Familiar
            </a>
            @endif

            <!-- Ministries (Collapsible) -->
            @if(\Nwidart\Modules\Facades\Module::isEnabled('Ministries'))
            <div class="space-y-1">
                <button @click="ministriesOpen = !ministriesOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['ministries'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="church" class="w-5 h-5 mr-3 {{ $routes['ministries'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Ministérios</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': ministriesOpen }" />
                </button>
                <div x-show="ministriesOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('admin.ministries.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ministries.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="list" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Lista de Ministérios
                        </a>
                        <a href="{{ route('admin.ministries.create') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ministries.create') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="plus" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Novo Ministério
                        </a>
                        <a href="{{ route('admin.ministries.plans.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ministries.plans*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="diagram-project" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Planos Estratégicos
                        </a>
                    </div>
                </div>
            </div>
            @endif


            <!-- HomePage (Collapsible) -->
            <div class="space-y-1">
                <button @click="homepageOpen = !homepageOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['homepage'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="house" class="w-5 h-5 mr-3 {{ $routes['homepage'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>HomePage</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': homepageOpen }" />
                </button>
                <div x-show="homepageOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                         <a href="{{ route('admin.homepage.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.homepage.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="pen-to-square" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Gerenciar Conteúdo
                        </a>
                        <a href="{{ route('admin.homepage.carousel.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.homepage.carousel*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="images" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Gerenciar Carousel
                        </a>
                        <a href="{{ route('admin.homepage.contacts.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.homepage.contacts*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="envelope" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Central de Contatos
                        </a>
                        <a href="{{ route('admin.homepage.newsletter.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.homepage.newsletter*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="newspaper" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Newsletter
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bible (Collapsible) -->
            <div class="space-y-1">
                <button @click="bibleOpen = !bibleOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['bible'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="book-bible" class="w-5 h-5 mr-3 {{ $routes['bible'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Bíblia Digital</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': bibleOpen }" />
                </button>
                <div x-show="bibleOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('admin.bible.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bible.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="book-open" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Versões e Livros
                        </a>
                        <a href="{{ route('admin.bible.import') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bible.import*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="file-import" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Importação
                        </a>
                        <a href="{{ route('admin.bible.plans.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bible.plans.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="calendar-check" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Planos de Leitura
                        </a>
                        <a href="{{ route('admin.bible.reports.church-plan') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bible.reports.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="chart-line" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Relatório Plano da Igreja
                        </a>
                    </div>
                </div>
            </div>


            <!-- Notifications -->
            <a href="{{ route('admin.notifications.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.notifications*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="bell" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.notifications*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Notificações
            </a>

            @if(auth()->user()->isAdmin())
                <!-- Password Resets Monitoring -->
                <a href="{{ route('admin.password-resets.index') }}"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.password-resets*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <x-icon name="key" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.password-resets*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                    Monitoramento de Senhas
                </a>
            @endif

            <div class="pt-4 pb-2">
                <p class="px-4 text-[11px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-wider">Educação e Eventos</p>
            </div>






            <!-- Sermons (Collapsible) -->
             <div class="space-y-1">
                <button @click="sermonsOpen = !sermonsOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['sermons'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="microphone-lines" class="w-5 h-5 mr-3 {{ $routes['sermons'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Sermões</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': sermonsOpen }" />
                </button>
                <div x-show="sermonsOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('admin.sermons.sermons.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sermons.sermons.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="list" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Listar Sermões
                        </a>
                        <a href="{{ route('admin.sermons.sermons.create') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sermons.sermons.create') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="plus" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Novo Sermão
                        </a>
                        <a href="{{ route('admin.sermons.categories.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sermons.categories*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="tags" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Categorias
                        </a>
                        <a href="{{ route('admin.sermons.series.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sermons.series*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="layer-group" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Séries
                        </a>
                        <a href="{{ route('admin.sermons.studies.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sermons.studies*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="book-open-reader" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Estudos
                        </a>
                        <a href="{{ route('admin.sermons.commentaries.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sermons.commentaries*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="comments" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Comentários
                        </a>
                    </div>
                </div>
            </div>



            <!-- Intercession (Collapsible) -->
            <div class="space-y-1">
                <button @click="intercessorOpen = !intercessorOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.intercessor*') ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="hands-praying" style="duotone" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.intercessor*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Intercessão</span>
                    </div>
                    <x-icon name="chevron-down" style="duotone" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': intercessorOpen }" />
                </button>
                <div x-show="intercessorOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('admin.intercessor.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.intercessor.dashboard') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="chart-pie" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Dashboard
                        </a>
                        <a href="{{ route('admin.intercessor.moderation.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.intercessor.moderation.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="clipboard-check" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Moderação de Pedidos
                        </a>
                        <a href="{{ route('admin.intercessor.moderation.testimonies.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.intercessor.moderation.testimonies.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="quote-right" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Moderação de Testemunhos
                        </a>
                        <a href="{{ route('admin.intercessor.categories.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.intercessor.categories*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="tags" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Categorias
                        </a>
                        <a href="{{ route('admin.intercessor.team.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.intercessor.team*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="users" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Gerenciar Intercessores
                        </a>
                        <a href="{{ route('admin.intercessor.reports.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.intercessor.reports*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="chart-line" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Relatórios de Intercessão
                        </a>
                        <a href="{{ route('admin.intercessor.settings.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.intercessor.settings*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="gears" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Configurações
                        </a>
                    </div>
                </div>
            </div>

            <!-- Worship / Louvor (Collapsible) -->
            <div class="space-y-1">
                <button @click="worshipOpen = !worshipOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['worship'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="music" class="w-5 h-5 mr-3 {{ $routes['worship'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Louvor</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': worshipOpen }" />
                </button>
                <div x-show="worshipOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('worship.admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('worship.admin.dashboard') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="chart-pie" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Dashboard
                        </a>
                        <a href="{{ route('worship.admin.setlists.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('worship.admin.setlists.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="clipboard-list" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Cultos e Repertórios
                        </a>
                        <a href="{{ route('worship.admin.songs.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('worship.admin.songs.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="music" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Músicas
                        </a>
                        <a href="{{ route('worship.admin.rosters.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('worship.admin.rosters.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="calendar-days" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Escalas
                        </a>
                        <a href="{{ route('worship.admin.instruments.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('worship.admin.instruments.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="guitar" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Instrumentos
                        </a>
                        <a href="{{ route('worship.admin.categories.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('worship.admin.categories.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="tags" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Categorias
                        </a>
                        <a href="{{ route('worship.admin.academy.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('worship.admin.academy.*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="graduation-cap" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Worship Academy
                        </a>
                    </div>
                </div>
            </div>


            <!-- Events (Collapsible) -->
            @can('viewAny', \Modules\Events\App\Models\Event::class)
             <div class="space-y-1">
                <button @click="eventsOpen = !eventsOpen"
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ $routes['events'] ? 'bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    <div class="flex items-center">
                        <x-icon name="calendar-days" class="w-5 h-5 mr-3 {{ $routes['events'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                        <span>Eventos</span>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': eventsOpen }" />
                </button>
                <div x-show="eventsOpen" style="display: none;">
                    <div class="pl-12 pr-4 space-y-1 mt-1">
                        <a href="{{ route('admin.events.events.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.events.events.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="list" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Listar Eventos
                        </a>
                        <a href="{{ route('admin.events.events.create') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.events.events.create') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="plus" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Novo Evento
                        </a>
                        @can('checkin', \Modules\Events\App\Models\Event::class)
                        <a href="{{ route('admin.events.checkin.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('admin.events.checkin.index') ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300' }}">
                            <x-icon name="qrcode" style="duotone" class="w-3.5 h-3.5 shrink-0" />
                            Check-in (Scanner)
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            @endcan


            <!-- Gamification Section -->
             <div class="pt-4 pb-2">
                <p class="px-4 text-[11px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-wider">Gamificação</p>
            </div>

            <a href="{{ route('admin.badges.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.badges*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="badge-check" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.badges*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Badges
            </a>

            <a href="{{ route('admin.gamification-levels.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.gamification-levels*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="layer-group" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.gamification-levels*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Níveis
            </a>

            <a href="{{ route('admin.cbav-bot.settings.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.cbav-bot*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="robot" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.cbav-bot*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Bot Elias
            </a>

            <!-- Financial Section -->
             <div class="pt-4 pb-2">
                <p class="px-4 text-[11px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-wider">Financeiro</p>
            </div>

            <a href="{{ route('admin.transactions.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.transactions*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="money-bill-transfer" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.transactions*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Transações
            </a>

            <a href="{{ route('admin.payment-gateways.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.payment-gateways*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                <x-icon name="credit-card" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.payment-gateways*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-500' }} transition-colors" />
                Gateways
            </a>


            <!-- Bottom Section -->
            <div class="mt-8 pt-4 border-t border-gray-100 dark:border-gray-800">
                <a href="{{ route('memberpanel.dashboard') }}"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200 transition-all duration-200 group">
                    <x-icon name="arrow-left" class="w-5 h-5 mr-3 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors" />
                    Painel de Membros
                </a>
            </div>


            <!-- Copyright -->
            <div class="px-6 py-4 mt-auto">
                <p class="text-[9px] text-center text-gray-400 dark:text-gray-600 font-bold uppercase tracking-widest">
                    Vertex Solutions © {{ date('Y') }}
                </p>
            </div>

        </nav>
    </div>
</aside>

<!-- Sidebar Overlay (Mobile) -->
<div id="sidebar-overlay" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-30 lg:hidden transition-opacity" style="z-index: 30;"></div>
