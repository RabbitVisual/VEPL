<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas do Painel do Membro (Centralizadas)
|--------------------------------------------------------------------------
|
| Todas as rotas destinadas a usuários autenticados (membros) no painel
| /painel. Middleware: auth, verified. Organizado por módulo para
| manutenção e segurança global.
|
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // =====================================================================
    // MemberPanel (Core): Dashboard, Perfil, Ministérios, Notificações, Bíblia, Tesouraria
    // =====================================================================
    Route::prefix('painel')->name('memberpanel.')->group(function () {
        // Dashboard
        Route::get('/', [\Modules\MemberPanel\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [\Modules\MemberPanel\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');

        // Perfil
        Route::get('/perfil', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/perfil/editar', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/perfil', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/perfil/fotos/{photo}/set-active', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'setActivePhoto'])->name('profile.photo.active');
        Route::delete('/perfil/fotos/{photo}', [\Modules\MemberPanel\App\Http\Controllers\ProfileController::class, 'deletePhoto'])->name('profile.photo.destroy');

        // Vínculos familiares — listar convites pendentes, solicitar novo vínculo, aceitar/recusar
        Route::get('/vinculos', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'pending'])->name('relationships.pending');
        Route::get('/vinculos/criar', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'create'])->name('relationships.create');
        Route::post('/vinculos', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'store'])->name('relationships.store');
        Route::get('/vinculos/buscar-cpf', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'searchMemberByCpf'])->name('relationships.search-cpf');
        Route::post('/vinculos/{user_relationship}/aceitar', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'accept'])->name('relationships.accept');
        Route::post('/vinculos/{user_relationship}/recusar', [\Modules\MemberPanel\App\Http\Controllers\RelationshipController::class, 'reject'])->name('relationships.reject');

        // Ministérios
        Route::get('/ministerios', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'index'])->name('ministries.index');
        Route::get('/ministerios/{ministry}', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'show'])->name('ministries.show');
        Route::post('/ministerios/{ministry}/join', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'join'])->name('ministries.join');
        Route::post('/ministerios/{ministry}/leave', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'leave'])->name('ministries.leave');
        Route::post('/ministerios/{ministry}/solicitacoes/{user}/aceitar', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'acceptRequest'])->name('ministries.requests.accept');
        Route::post('/ministerios/{ministry}/solicitacoes/{user}/recusar', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'rejectRequest'])->name('ministries.requests.reject');
        Route::get('/ministerios/{ministry}/relatorios/criar', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'createReport'])->name('ministries.reports.create');
        Route::post('/ministerios/{ministry}/relatorios', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'storeReport'])->name('ministries.reports.store');
        Route::get('/ministerios/{ministry}/relatorios/{report}/editar', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'editReport'])->name('ministries.reports.edit');
        Route::put('/ministerios/{ministry}/relatorios/{report}', [\Modules\Ministries\App\Http\Controllers\Member\MinistryController::class, 'updateReport'])->name('ministries.reports.update');


        // Notificações
        Route::get('/notificacoes', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notificacoes/{notification}/read', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notificacoes/read-all', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::delete('/notificacoes/clear-all', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
        Route::delete('/notificacoes/{notification}', [\Modules\MemberPanel\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Preferências de notificações (Notifications Engine v2)
        Route::get('/preferencias/notificacoes', [\Modules\Notifications\App\Http\Controllers\MemberPanel\NotificationPreferencesController::class, 'index'])->name('preferences.notifications.index');
        Route::put('/preferencias/notificacoes', [\Modules\Notifications\App\Http\Controllers\MemberPanel\NotificationPreferencesController::class, 'update'])->name('preferences.notifications.update');

        // Bíblia (rotas específicas primeiro para evitar conflito com {version?})
        Route::get('/biblia/interlinear', [\Modules\Bible\App\Http\Controllers\InterlinearController::class, 'index'])->name('bible.interlinear');
        Route::get('/biblia/interlinear/data', [\Modules\Bible\App\Http\Controllers\InterlinearController::class, 'getData'])->name('bible.interlinear.data');
        Route::get('/biblia/interlinear/books', [\Modules\Bible\App\Http\Controllers\InterlinearController::class, 'getBooksMetadata'])->name('bible.interlinear.books');
        Route::get('/biblia/strong/{number}', [\Modules\Bible\App\Http\Controllers\InterlinearController::class, 'getStrongDefinition'])->name('bible.strong.show');
        Route::get('/biblia', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'index'])->name('bible.index');
        Route::get('/biblia/buscar', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'search'])->name('bible.search');
        Route::get('/biblia/favoritos', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'favorites'])->name('bible.favorites');
        Route::post('/biblia/versiculo/{verse}/favorito', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'addFavorite'])->name('bible.favorite.add');
        Route::delete('/biblia/versiculo/{verse}/favorito', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'removeFavorite'])->name('bible.favorite.remove');
        Route::get('/biblia/{version?}', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'read'])->name('bible.read');
        Route::get('/biblia/{version}/livro/{book}', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'showBook'])->name('bible.book');
        Route::get('/biblia/{version}/livro/{book}/capitulo/{chapter}', [\Modules\Bible\App\Http\Controllers\MemberPanel\BibleController::class, 'showChapter'])->name('bible.chapter');

        // Tesouraria (proxy no painel)
        Route::prefix('tesouraria')->name('treasury.')->group(function () {
            $treasuryController = \Modules\Treasury\App\Http\Controllers\MemberPanel\TreasuryController::class;
            Route::get('/', [$treasuryController, 'dashboard'])->name('dashboard');
            Route::get('/dashboard', [$treasuryController, 'dashboard'])->name('dashboard.index');
            Route::get('/transparencia', [$treasuryController, 'transparency'])->name('transparency');
            Route::get('/entradas', [$treasuryController, 'entriesIndex'])->name('entries.index');
            Route::get('/entradas/criar', [$treasuryController, 'entriesCreate'])->name('entries.create');
            Route::post('/entradas', [$treasuryController, 'entriesStore'])->name('entries.store');
            Route::get('/entradas/{entry}/editar', [$treasuryController, 'entriesEdit'])->name('entries.edit');
            Route::put('/entradas/{entry}', [$treasuryController, 'entriesUpdate'])->name('entries.update');
            Route::delete('/entradas/{entry}', [$treasuryController, 'entriesDestroy'])->name('entries.destroy');
            Route::post('/entradas/importar', [$treasuryController, 'proxy'])->defaults('controller', 'entries')->defaults('method', 'importPayment')->name('import');
            Route::get('/campanhas', [$treasuryController, 'campaignsIndex'])->name('campaigns.index');
            Route::get('/campanhas/criar', [$treasuryController, 'campaignsCreate'])->name('campaigns.create');
            Route::post('/campanhas', [$treasuryController, 'campaignsStore'])->name('campaigns.store');
            Route::get('/campanhas/{campaign}', [$treasuryController, 'campaignsShow'])->name('campaigns.show');
            Route::get('/campanhas/{campaign}/editar', [$treasuryController, 'campaignsEdit'])->name('campaigns.edit');
            Route::put('/campanhas/{campaign}', [$treasuryController, 'campaignsUpdate'])->name('campaigns.update');
            Route::delete('/campanhas/{campaign}', [$treasuryController, 'campaignsDestroy'])->name('campaigns.destroy');
            Route::get('/metas', [$treasuryController, 'goalsIndex'])->name('goals.index');
            Route::get('/metas/criar', [$treasuryController, 'goalsCreate'])->name('goals.create');
            Route::post('/metas', [$treasuryController, 'goalsStore'])->name('goals.store');
            Route::get('/metas/{goal}', [$treasuryController, 'goalsShow'])->name('goals.show');
            Route::get('/metas/{goal}/editar', [$treasuryController, 'goalsEdit'])->name('goals.edit');
            Route::put('/metas/{goal}', [$treasuryController, 'goalsUpdate'])->name('goals.update');
            Route::delete('/metas/{goal}', [$treasuryController, 'goalsDestroy'])->name('goals.destroy');
            Route::get('/relatorios', [$treasuryController, 'reportsIndex'])->name('reports.index');
            Route::get('/relatorios/exportar/excel', [$treasuryController, 'reportsExportExcel'])->name('reports.export.excel');
            Route::get('/relatorios/exportar/pdf', [$treasuryController, 'reportsExportPdf'])->name('reports.export.pdf');
            Route::get('/relatorios/exportar', [$treasuryController, 'reportsExport'])->name('reports.export');
            Route::middleware(['admin'])->group(function () use ($treasuryController) {
                Route::get('/permissoes', [$treasuryController, 'permissionsIndex'])->name('permissions.index');
                Route::get('/permissoes/criar', [$treasuryController, 'permissionsCreate'])->name('permissions.create');
                Route::post('/permissoes', [$treasuryController, 'permissionsStore'])->name('permissions.store');
                Route::get('/permissoes/{treasuryPermission}/editar', [$treasuryController, 'permissionsEdit'])->name('permissions.edit');
                Route::put('/permissoes/{treasuryPermission}', [$treasuryController, 'permissionsUpdate'])->name('permissions.update');
                Route::delete('/permissoes/{treasuryPermission}', [$treasuryController, 'permissionsDestroy'])->name('permissions.destroy');
            });
        });

        // PaymentGateway - Doações
        Route::get('/minhas-doacoes', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'index'])->name('donations.index');
        Route::get('/doacoes', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'create'])->name('donations.create');
        Route::post('/doacoes', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'store'])->name('donations.store');
        Route::get('/doacoes/{transactionId}', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'show'])->name('donations.show');
        Route::get('/doacoes/{transactionId}/retry', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'retry'])->name('donations.retry');
        Route::post('/doacoes/{transactionId}/retry', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'updateGateway'])->name('donations.update-gateway');
        Route::get('/doacoes/{transactionId}/status', [\Modules\PaymentGateway\App\Http\Controllers\MemberPanel\DonationController::class, 'checkStatus'])->name('donations.check-status');
    });


    // =====================================================================
    // Events - Inscrições e eventos (painel/eventos)
    // =====================================================================
    Route::prefix('painel/eventos')->name('memberpanel.events.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/minhas-inscricoes', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'myRegistrations'])->name('my-registrations');
        Route::get('/minhas-inscricoes/{registration}', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'showRegistration'])->name('show-registration');
        Route::get('/', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'index'])->name('index');
        Route::get('/{event:slug}', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'show'])->name('show');
        Route::post('/{event:slug}/register', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'register'])->name('register');
        Route::get('/registration/{registration}/retry', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'retryRegistration'])->name('registration.retry');
        Route::post('/registration/{registration}/retry', [\Modules\Events\App\Http\Controllers\MemberPanel\EventController::class, 'updateRegistrationGateway'])->name('registration.update-gateway');
        Route::get('/registration/{registration}/pending', function ($registration) {
            $registration = \Modules\Events\App\Models\EventRegistration::findOrFail($registration);
            if ($registration->user_id !== auth()->id()) {
                abort(403);
            }
            return view('events::memberpanel.registration.pending', compact('registration'));
        })->name('registration.pending');
        Route::get('/registration/{registration}/confirmed', function ($registration) {
            $registration = \Modules\Events\App\Models\EventRegistration::with(['event', 'participants'])->findOrFail($registration);
            if ($registration->user_id !== auth()->id()) {
                abort(403);
            }
            return view('events::memberpanel.registration.confirmed', compact('registration'));
        })->name('registration.confirmed');
    });


    // =====================================================================
    // Academy - LMS (painel/academia)
    // =====================================================================
    Route::prefix('painel/academia')->name('memberpanel.academy.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/curso/{course}', [\Modules\Academy\App\Http\Controllers\MemberPanel\ClassroomController::class, 'show'])->name('classroom');
        Route::get('/curso/{course}/aula/{lesson}', [\Modules\Academy\App\Http\Controllers\MemberPanel\ClassroomController::class, 'show'])->name('classroom.lesson');
    });

    // =====================================================================
    // Community - Fórum (painel/comunidade)
    // =====================================================================
    Route::prefix('painel/comunidade')->name('memberpanel.community.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/', [\Modules\Community\App\Http\Controllers\MemberPanel\CommunityFeedController::class, 'index'])->name('index');
    });

    Route::prefix('painel/nepesearch')->name('memberpanel.nepesearch.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/buscar', [\Modules\NepeSearch\App\Http\Controllers\NepeSearchController::class, 'search'])->name('search');
    });

    // =====================================================================
    // Sermons - Estúdio de Pregação Expositiva (painel/sermoes)
    // =====================================================================
    Route::prefix('painel/sermoes')->name('memberpanel.sermons.')->group(function () {
        Route::get('/', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'index'])->name('index');
        Route::get('/meus-sermoes', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'mySermons'])->name('my-sermons');
        Route::get('/meus-favoritos', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'myFavorites'])->name('my-favorites');
        Route::get('/criar', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'create'])->name('create');
        Route::post('/', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'store'])->name('store');
        Route::get('/convite/{collaborator}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'showCollaboratorInvite'])->name('collaborator.invite');
        Route::post('/convite/{collaborator}/respond', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'respondCollaborator'])->name('collaborator.respond');
        Route::get('/{sermon}/export-pdf', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/{sermon}/editar', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'edit'])->name('edit');
        Route::put('/{sermon}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'update'])->name('update');
        Route::delete('/{sermon}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'destroy'])->name('destroy');
        Route::get('/{sermon}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'show'])->name('show');
        Route::post('/{sermon}/favoritar', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'toggleFavorite'])->name('toggle-favorite');
        Route::post('/{sermon}/comentar', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonController::class, 'storeComment'])->name('store-comment');
    });
    Route::prefix('painel/series-expositivas')->name('memberpanel.sermon-series.')->group(function () {
        Route::get('/', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonSeriesController::class, 'index'])->name('index');
        Route::get('/{series}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonSeriesController::class, 'show'])->name('show');
    });
    Route::prefix('painel/esbocos-homileticos')->name('memberpanel.sermon-outlines.')->group(function () {
        Route::get('/', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonOutlineController::class, 'index'])->name('index');
        Route::get('/{study}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonOutlineController::class, 'show'])->name('show');
    });
    Route::prefix('painel/exegese-sermoes')->name('memberpanel.sermon-exegesis.')->group(function () {
        Route::get('/', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonExegesisController::class, 'index'])->name('index');
        Route::get('/{commentary}', [\Modules\Sermons\App\Http\Controllers\MemberPanel\SermonExegesisController::class, 'show'])->name('show');
    });


    // =====================================================================
    // Intercessor (painel/intercessor) - nome: member.intercessor.*
    // =====================================================================
    Route::middleware([\Modules\Intercessor\App\Http\Middleware\CheckIntercessorStatus::class])->prefix('painel/intercessor')->name('member.intercessor.')->group(function () {
        Route::get('/', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\IntercessorDashboardController::class, 'index'])->name('dashboard');
        Route::resource('requests', \Modules\Intercessor\App\Http\Controllers\MemberPanel\IntercessorController::class);
        Route::get('/mural-de-intercessao', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\PrayerRoomController::class, 'index'])->name('room.index');
        Route::get('/mural-de-testemunhos', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\PrayerRoomController::class, 'testimonies'])->name('room.testimonies');
        Route::get('/sala-de-guerra/{request}', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\PrayerRoomController::class, 'show'])->name('room.show');
        Route::post('/sala-de-guerra/{prayerRequest}/orar', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\PrayerRoomController::class, 'commit'])->name('room.commit');
        Route::post('/sala-de-guerra/{prayerRequest}/concluir', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\PrayerRoomController::class, 'finish'])->name('room.finish');
        Route::post('/sala-de-guerra/{prayerRequest}/interagir', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\PrayerRoomController::class, 'interact'])->name('room.interact');
        Route::get('/meus-pedidos/{request}/testemunhar', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\IntercessorController::class, 'testimony'])->name('requests.testimony');
        Route::post('/meus-pedidos/{request}/testemunhar', [\Modules\Intercessor\App\Http\Controllers\MemberPanel\IntercessorController::class, 'submitTestimony'])->name('requests.testimony.store');
    });

    // =====================================================================
    // Worship - Louvor (painel/louvor) - nome: worship.member.*
    // =====================================================================
    Route::prefix('painel/louvor')->name('worship.member.')->group(function () {
        Route::get('tocar/{setlist}', [\Modules\Worship\App\Http\Controllers\MemberPanel\MusicianStageController::class, 'view'])->name('stage.view');
        Route::get('minhas-escalas', [\Modules\Worship\App\Http\Controllers\MemberPanel\MyRosterController::class, 'index'])->name('rosters.index');
        Route::post('minhas-escalas/{roster}/status', [\Modules\Worship\App\Http\Controllers\MemberPanel\MyRosterController::class, 'updateStatus'])->name('rosters.status');
        Route::get('rehearsal', [\Modules\Worship\App\Http\Controllers\MemberPanel\RehearsalRoomController::class, 'index'])->name('rehearsal.index');
        Route::get('rehearsal/{setlist}', [\Modules\Worship\App\Http\Controllers\MemberPanel\RehearsalRoomController::class, 'show'])->name('rehearsal.show');
        Route::get('academy', [\Modules\Worship\App\Http\Controllers\MemberPanel\AcademyMemberController::class, 'index'])->name('academy.index');
        Route::get('academy/course/{id}/classroom', [\Modules\Worship\App\Http\Controllers\MemberPanel\AcademyMemberController::class, 'classroom'])->name('academy.classroom');
        Route::get('academy/course/{id}', function ($id) {
            return redirect()->route('worship.member.academy.classroom', $id);
        })->name('academy.course');
    });

});
