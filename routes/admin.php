<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas do Painel Admin (Centralizadas)
|--------------------------------------------------------------------------
|
| Todas as rotas destinadas a administradores (prefixo /admin).
| Middleware: auth, verified, admin. Organizado por módulo para
| manutenção e segurança global.
|
*/

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {

    // =====================================================================
    // PaymentGateway - Estatísticas e gateways (admin.*)
    // =====================================================================
    Route::name('admin.')->group(function () {
        Route::get('/payment-gateways/statistics', [\Modules\PaymentGateway\App\Http\Controllers\Admin\PaymentGatewayController::class, 'statistics'])->name('payment-gateways.statistics');
        Route::resource('payment-gateways', \Modules\PaymentGateway\App\Http\Controllers\Admin\PaymentGatewayController::class)->except(['create', 'store', 'destroy', 'show']);
        Route::get('/transactions', [\Modules\PaymentGateway\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{payment}', [\Modules\PaymentGateway\App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{payment}/comprovante', [\Modules\PaymentGateway\App\Http\Controllers\Admin\TransactionController::class, 'receipt'])->name('transactions.receipt');
        Route::post('/transactions/{payment}/cancel', [\Modules\PaymentGateway\App\Http\Controllers\Admin\TransactionController::class, 'cancel'])->name('transactions.cancel');
        Route::delete('/transactions/{payment}', [\Modules\PaymentGateway\App\Http\Controllers\Admin\TransactionController::class, 'destroy'])->name('transactions.destroy');
    });

    // =====================================================================
    // Admin (Core): Dashboard, Usuários, Módulos, Configurações, HomePage, Bíblia, Gamificação
    // =====================================================================
    Route::name('admin.')->group(function () {
        Route::get('/', [\Modules\Admin\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [\Modules\Admin\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');

        // Relatórios: Inteligência Familiar e Demografia (admin apenas)
        Route::prefix('relatorios')->name('reports.')->group(function () {
            Route::get('demografico-familiar', [\Modules\Admin\App\Http\Controllers\FamilyDemographicsController::class, 'index'])->name('family-demographics.index');
            Route::get('demografico-familiar/exportar/pdf', [\Modules\Admin\App\Http\Controllers\FamilyDemographicsController::class, 'exportPdf'])->name('family-demographics.export.pdf');
            Route::get('demografico-familiar/exportar/excel', [\Modules\Admin\App\Http\Controllers\FamilyDemographicsController::class, 'exportExcel'])->name('family-demographics.export.excel');
        });

        Route::middleware([\Modules\Admin\App\Http\Middleware\EnsureUserIsTechnicalAdmin::class])->group(function () {
            Route::get('/modules', [\Modules\Admin\App\Http\Controllers\ModuleController::class, 'index'])->name('modules.index');
            Route::post('/modules/{module}/enable', [\Modules\Admin\App\Http\Controllers\ModuleController::class, 'enable'])->name('modules.enable');
            Route::post('/modules/{module}/disable', [\Modules\Admin\App\Http\Controllers\ModuleController::class, 'disable'])->name('modules.disable');
            Route::resource('cep-ranges', \Modules\Admin\App\Http\Controllers\CepRangeController::class);
        });

        Route::get('users/import', [\Modules\Admin\App\Http\Controllers\MemberImportController::class, 'showImportForm'])->name('users.import');
        Route::post('users/import', [\Modules\Admin\App\Http\Controllers\MemberImportController::class, 'import'])->name('users.import.post');
        Route::get('users/import/template', [\Modules\Admin\App\Http\Controllers\MemberImportController::class, 'downloadTemplate'])->name('users.import.template');
        Route::get('api/users/search', [\Modules\Admin\App\Http\Controllers\UserController::class, 'search'])->name('api.users.search');
        Route::get('api/users/search-by-cpf', [\Modules\Admin\App\Http\Controllers\UserController::class, 'searchByCpf'])->name('api.users.search-by-cpf');
        Route::get('users/{user}/family-tree-analysis', [\Modules\Admin\App\Http\Controllers\UserController::class, 'familyTreeAnalysis'])->name('users.family-tree-analysis');
        Route::post('users/relationships/{user_relationship}/accept', [\Modules\Admin\App\Http\Controllers\UserRelationshipController::class, 'accept'])->name('users.relationships.accept');
        Route::post('users/relationships/{user_relationship}/reject', [\Modules\Admin\App\Http\Controllers\UserRelationshipController::class, 'reject'])->name('users.relationships.reject');
        Route::resource('users', \Modules\Admin\App\Http\Controllers\UserController::class);

        Route::get('/profile', [\Modules\Admin\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [\Modules\Admin\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\Modules\Admin\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        // 2FA TOTP (perfil do administrador)
        Route::get('/profile/2fa', [\Modules\Admin\App\Http\Controllers\TwoFactorController::class, 'show'])->name('profile.2fa.show');
        Route::post('/profile/2fa/setup', [\Modules\Admin\App\Http\Controllers\TwoFactorController::class, 'setup'])->name('profile.2fa.setup');
        Route::post('/profile/2fa/confirm', [\Modules\Admin\App\Http\Controllers\TwoFactorController::class, 'confirm'])->name('profile.2fa.confirm');
        Route::post('/profile/2fa/disable', [\Modules\Admin\App\Http\Controllers\TwoFactorController::class, 'disable'])->name('profile.2fa.disable');
        Route::get('/profile/2fa/qr', [\Modules\Admin\App\Http\Controllers\TwoFactorController::class, 'qrImage'])->name('profile.2fa.qr');

        Route::middleware([\Modules\Admin\App\Http\Middleware\EnsureUserIsTechnicalAdmin::class])->group(function () {
            Route::get('/password-resets', [\Modules\Admin\App\Http\Controllers\PasswordResetController::class, 'index'])->name('password-resets.index');
            Route::get('/password-resets/settings', [\Modules\Admin\App\Http\Controllers\PasswordResetController::class, 'settings'])->name('password-resets.settings');
            Route::put('/password-resets/settings', [\Modules\Admin\App\Http\Controllers\PasswordResetController::class, 'updateSettings'])->name('password-resets.settings.update');
            Route::get('/settings', [\Modules\Admin\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
            Route::put('/settings', [\Modules\Admin\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
            Route::post('/settings/test-email', [\Modules\Admin\App\Http\Controllers\SettingsController::class, 'testEmail'])->name('settings.test-email');
            Route::post('/settings/activate-maintenance', [\Modules\Admin\App\Http\Controllers\SettingsController::class, 'activateMaintenance'])->name('settings.activate-maintenance');
            Route::post('/settings/deactivate-maintenance', [\Modules\Admin\App\Http\Controllers\SettingsController::class, 'deactivateMaintenance'])->name('settings.deactivate-maintenance');
        });

        // Ministries: declarar rotas estáticas ANTES do resource para não capturar "plans" como {ministry}
        Route::get('/ministries/plans', [\Modules\Ministries\App\Http\Controllers\Admin\MinistryPlanController::class, 'index'])->name('ministries.plans.index');
        Route::get('/ministries/{ministry}/plans/create', [\Modules\Ministries\App\Http\Controllers\Admin\MinistryPlanController::class, 'create'])->name('ministries.plans.create');
        Route::post('/ministries/{ministry}/plans', [\Modules\Ministries\App\Http\Controllers\Admin\MinistryPlanController::class, 'store'])->name('ministries.plans.store');
        Route::get('/ministries/{ministry}/plans/{plan}', [\Modules\Ministries\App\Http\Controllers\Admin\MinistryPlanController::class, 'show'])->name('ministries.plans.show');
        Route::get('/ministries/{ministry}/plans/{plan}/edit', [\Modules\Ministries\App\Http\Controllers\Admin\MinistryPlanController::class, 'edit'])->name('ministries.plans.edit');
        Route::put('/ministries/{ministry}/plans/{plan}', [\Modules\Ministries\App\Http\Controllers\Admin\MinistryPlanController::class, 'update'])->name('ministries.plans.update');
        Route::post('/ministries/{ministry}/plans/{plan}/submit-for-approval', [\Modules\Ministries\App\Http\Controllers\Admin\MinistryPlanController::class, 'submitForApproval'])->name('ministries.plans.submit-for-approval');
        Route::delete('/ministries/{ministry}/plans/{plan}', [\Modules\Ministries\App\Http\Controllers\Admin\MinistryPlanController::class, 'destroy'])->name('ministries.plans.destroy');
        Route::post('/ministries/{ministry}/plans/{plan}/generate-event', [\Modules\Ministries\App\Http\Controllers\Admin\MinistryPlanController::class, 'generateEvent'])->name('ministries.plans.generate-event');
        Route::post('/ministries/{ministry}/plans/{plan}/generate-events', [\Modules\Ministries\App\Http\Controllers\Admin\MinistryPlanController::class, 'generateEvents'])->name('ministries.plans.generate-events');
        Route::get('/ministries/{ministry}/relatorio-consolidado', [\Modules\Ministries\App\Http\Controllers\Admin\MinistryReportController::class, 'exportConsolidated'])->name('ministries.reports.consolidated');
        Route::resource('ministries', \Modules\Ministries\App\Http\Controllers\Admin\MinistryController::class);
        Route::post('/ministries/{ministry}/members', [\Modules\Ministries\App\Http\Controllers\Admin\MinistryController::class, 'addMember'])->name('ministries.members.add');
        Route::delete('/ministries/{ministry}/members/{user}', [\Modules\Ministries\App\Http\Controllers\Admin\MinistryController::class, 'removeMember'])->name('ministries.members.remove');
        Route::post('/ministries/{ministry}/members/{user}/approve', [\Modules\Ministries\App\Http\Controllers\Admin\MinistryController::class, 'approveMember'])->name('ministries.members.approve');
        Route::delete('/notifications/clear-my-inbox', [\Modules\Admin\App\Http\Controllers\NotificationController::class, 'clearMyInbox'])->name('notifications.clear-my-inbox');
        // Notifications Engine v2 - Control Room
        Route::get('/notifications/control/dashboard', [\Modules\Notifications\App\Http\Controllers\Admin\NotificationDashboardController::class, 'index'])->name('notifications.control.dashboard');
        Route::get('/notifications/control/dlq', [\Modules\Notifications\App\Http\Controllers\Admin\NotificationDlqController::class, 'index'])->name('notifications.dlq.index');
        Route::post('/notifications/control/dlq/{failed}/retry', [\Modules\Notifications\App\Http\Controllers\Admin\NotificationDlqController::class, 'retry'])->name('notifications.dlq.retry');
        Route::get('/notifications/control/broadcast', [\Modules\Notifications\App\Http\Controllers\Admin\NotificationBroadcastController::class, 'create'])->name('notifications.broadcast.create');
        Route::post('/notifications/control/broadcast', [\Modules\Notifications\App\Http\Controllers\Admin\NotificationBroadcastController::class, 'store'])->name('notifications.broadcast.store');
        Route::resource('notifications/templates', \Modules\Notifications\App\Http\Controllers\Admin\NotificationTemplatesController::class)->parameters(['templates' => 'template'])->names('notifications.templates');
        Route::resource('notifications', \Modules\Admin\App\Http\Controllers\NotificationController::class);

        Route::prefix('homepage')->name('homepage.')->group(function () {
            Route::get('/', function () {
                return redirect()->route('admin.homepage.settings.index');
            })->name('index');
            Route::get('/settings', [\Modules\Admin\App\Http\Controllers\HomePageSettingsController::class, 'index'])->name('settings.index');
            Route::put('/settings', [\Modules\Admin\App\Http\Controllers\HomePageSettingsController::class, 'update'])->name('settings.update');
            Route::resource('carousel', \Modules\Admin\App\Http\Controllers\CarouselController::class)->except(['show'])->parameters(['carousel' => 'slide']);
            Route::post('/carousel/order', [\Modules\Admin\App\Http\Controllers\CarouselController::class, 'updateOrder'])->name('carousel.order');
            Route::post('/carousel/{slide}/toggle', [\Modules\Admin\App\Http\Controllers\CarouselController::class, 'toggleActive'])->name('carousel.toggle');
            Route::post('/carousel/{slide}/duplicate', [\Modules\Admin\App\Http\Controllers\CarouselController::class, 'duplicate'])->name('carousel.duplicate');
            Route::post('contacts/mark-read', [\Modules\Admin\App\Http\Controllers\ContactController::class, 'markRead'])->name('contacts.mark-read');
            Route::resource('contacts', \Modules\Admin\App\Http\Controllers\ContactController::class);
            Route::get('newsletter/export', [\Modules\Admin\App\Http\Controllers\NewsletterController::class, 'export'])->name('newsletter.export');
            Route::resource('newsletter', \Modules\Admin\App\Http\Controllers\NewsletterController::class);
            Route::post('/newsletter/send', [\Modules\Admin\App\Http\Controllers\NewsletterController::class, 'send'])->name('newsletter.send');
        });

        // =====================================================================
        // Bible Module - Planos e import (admin.bible.*)
        // =====================================================================
        Route::prefix('bible')->name('bible.')->group(function () {
            Route::get('import', [\Modules\Bible\App\Http\Controllers\BibleController::class, 'import'])->name('import');
            Route::post('import', [\Modules\Bible\App\Http\Controllers\BibleController::class, 'storeImport'])->name('import.store');
            Route::resource('plans', \Modules\Bible\App\Http\Controllers\Admin\BiblePlanController::class);
            Route::resource('metadata', \Modules\Bible\App\Http\Controllers\Admin\BibleMetadataController::class)
                ->parameters(['metadata' => 'metadatum'])
                ->only(['index', 'create', 'store', 'edit', 'update']);
            Route::get('metadata/options/books', [\Modules\Bible\App\Http\Controllers\Admin\BibleMetadataController::class, 'booksByVersion'])->name('metadata.options.books');
            Route::get('metadata/options/chapters', [\Modules\Bible\App\Http\Controllers\Admin\BibleMetadataController::class, 'chaptersByBook'])->name('metadata.options.chapters');
            Route::get('metadata/options/verses', [\Modules\Bible\App\Http\Controllers\Admin\BibleMetadataController::class, 'versesByChapter'])->name('metadata.options.verses');
            Route::get('plans/{id}/generate', [\Modules\Bible\App\Http\Controllers\Admin\BiblePlanController::class, 'generator'])->name('plans.generate');
            Route::post('plans/{id}/generate', [\Modules\Bible\App\Http\Controllers\Admin\BiblePlanController::class, 'processGeneration'])->name('plans.process-generation');
            Route::get('plans/{planId}/days/{dayId}/edit', [\Modules\Bible\App\Http\Controllers\Admin\BiblePlanController::class, 'editDay'])->name('plans.days.edit');
            Route::post('plans/days/{dayId}/content', [\Modules\Bible\App\Http\Controllers\Admin\BiblePlanController::class, 'storeContent'])->name('plans.content.store');
            Route::put('plans/content/{contentId}', [\Modules\Bible\App\Http\Controllers\Admin\BiblePlanController::class, 'updateContent'])->name('plans.content.update');
            Route::delete('plans/content/{contentId}', [\Modules\Bible\App\Http\Controllers\Admin\BiblePlanController::class, 'destroyContent'])->name('plans.content.destroy');
            Route::get('reports/church-plan', [\Modules\Bible\App\Http\Controllers\Admin\BibleReportController::class, 'churchPlan'])->name('reports.church-plan');

            // Strong's Lexicon (CRUD permitido)
            Route::resource('strongs-lexicon', \Modules\Bible\App\Http\Controllers\Admin\StrongsLexiconController::class)->parameters([
                'strongs-lexicon' => 'lexicon',
            ]);

            // Strong's Corrections (aprovação e aplicação no lexicon)
            Route::get('strongs-corrections', [\Modules\Bible\App\Http\Controllers\Admin\StrongsCorrectionsController::class, 'index'])
                ->name('strongs-corrections.index');
            Route::get('strongs-corrections/{id}', [\Modules\Bible\App\Http\Controllers\Admin\StrongsCorrectionsController::class, 'show'])
                ->name('strongs-corrections.show');
            Route::post('strongs-corrections/{id}/approve', [\Modules\Bible\App\Http\Controllers\Admin\StrongsCorrectionsController::class, 'approve'])
                ->name('strongs-corrections.approve');
            Route::post('strongs-corrections/{id}/reject', [\Modules\Bible\App\Http\Controllers\Admin\StrongsCorrectionsController::class, 'reject'])
                ->name('strongs-corrections.reject');

            // Bible panoramas por livro (CRUD permitido)
            Route::resource('panoramas', \Modules\Bible\App\Http\Controllers\Admin\BibleBookPanoramaAdminController::class)->parameters([
                'panoramas' => 'panorama',
            ]);

            // Bible interlinear word-tags (CRUD com paginação; recomendado usar filtros)
            Route::resource('word-tags', \Modules\Bible\App\Http\Controllers\Admin\BibleWordTagsController::class)->only([
                'index', 'create', 'store', 'show', 'edit', 'update', 'destroy',
            ])->parameters([
                'word-tags' => 'wordTag',
            ]);
        });

        Route::resource('bible', \Modules\Bible\App\Http\Controllers\Admin\BibleController::class);
        Route::get('/bible/import', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'importForm'])->name('bible.import');
        Route::post('/bible/import', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'store'])->name('bible.import.store');
        Route::get('/bible/{version}/book/{book}', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'viewBook'])->name('bible.book');
        Route::get('/bible/{version}/book/{book}/chapter/{chapter}', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'viewChapter'])->name('bible.chapter');
        Route::get('/bible/{bible}/chapter-audio', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'chapterAudioIndex'])->name('bible.chapter-audio.index');
        Route::get('/bible/{bible}/chapter-audio/template', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'chapterAudioTemplate'])->name('bible.chapter-audio.template');
        Route::post('/bible/{bible}/chapter-audio', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'chapterAudioStore'])->name('bible.chapter-audio.store');
        Route::delete('/bible/{bible}/chapter-audio/{chapter_audio}', [\Modules\Bible\App\Http\Controllers\Admin\BibleController::class, 'chapterAudioDestroy'])->name('bible.chapter-audio.destroy');

    });


    // =====================================================================
    // Sermons - Admin (admin.sermons.*)
    // =====================================================================
    Route::prefix('sermons')->name('admin.sermons.')->group(function () {
        Route::resource('sermons', \Modules\Sermons\App\Http\Controllers\Admin\SermonController::class)->except(['show']);
        Route::get('/sermons/{sermon}', [\Modules\Sermons\App\Http\Controllers\Admin\SermonController::class, 'show'])->name('sermons.show');
        Route::get('/sermons/{sermon}/export-pdf', [\Modules\Sermons\App\Http\Controllers\Admin\SermonController::class, 'exportPdf'])->name('sermons.export-pdf');
        Route::post('/sermons/{sermon}/collaborators', [\Modules\Sermons\App\Http\Controllers\Admin\SermonController::class, 'inviteCollaborator'])->name('sermons.collaborators.invite');
        Route::resource('categories', \Modules\Sermons\App\Http\Controllers\Admin\CategoryController::class)->except(['show']);
        Route::resource('series', \Modules\Sermons\App\Http\Controllers\Admin\SermonSeriesController::class);
        Route::resource('studies', \Modules\Sermons\App\Http\Controllers\Admin\SermonOutlineController::class);
        Route::resource('commentaries', \Modules\Sermons\App\Http\Controllers\Admin\SermonExegesisController::class);
    });

    // =====================================================================
    // Events - Admin unificado (admin.events.*)
    // =====================================================================
    Route::prefix('events')->name('admin.events.')->group(function () {
        Route::resource('events', \Modules\Events\App\Http\Controllers\Admin\EventController::class);
        Route::post('events/{event}/duplicate', [\Modules\Events\App\Http\Controllers\Admin\EventController::class, 'duplicate'])->name('events.duplicate');
        Route::post('events/{event}/batches', [\Modules\Events\App\Http\Controllers\Admin\EventController::class, 'storeBatch'])->name('events.batches.store');
        Route::put('events/{event}/batches/{batch}', [\Modules\Events\App\Http\Controllers\Admin\EventController::class, 'updateBatch'])->name('events.batches.update');
        Route::delete('events/{event}/batches/{batch}', [\Modules\Events\App\Http\Controllers\Admin\EventController::class, 'destroyBatch'])->name('events.batches.destroy');
        Route::get('checkin', [\Modules\Events\App\Http\Controllers\Admin\CheckinController::class, 'index'])->name('checkin.index');
        Route::post('checkin/validate', [\Modules\Events\App\Http\Controllers\Admin\CheckinController::class, 'validateCheckin'])->name('checkin.validate');
        Route::prefix('events/{event}')->name('events.')->group(function () {
            Route::get('/registrations', [\Modules\Events\App\Http\Controllers\Admin\RegistrationController::class, 'index'])->name('registrations.index');
            Route::get('/registrations/{registration}', [\Modules\Events\App\Http\Controllers\Admin\RegistrationController::class, 'show'])->name('registrations.show');
            Route::post('/registrations/{registration}/confirm', [\Modules\Events\App\Http\Controllers\Admin\RegistrationController::class, 'confirm'])->name('registrations.confirm');
            Route::post('/registrations/{registration}/cancel', [\Modules\Events\App\Http\Controllers\Admin\RegistrationController::class, 'cancel'])->name('registrations.cancel');
            Route::get('/registrations/export/pdf', [\Modules\Events\App\Http\Controllers\Admin\RegistrationController::class, 'exportPdf'])->name('registrations.export-pdf');
            Route::get('/registrations/export/badges', [\Modules\Events\App\Http\Controllers\Admin\RegistrationController::class, 'exportBadges'])->name('registrations.export-badges');
            Route::get('/registrations/export/excel', [\Modules\Events\App\Http\Controllers\Admin\RegistrationController::class, 'exportExcel'])->name('registrations.export-excel');
        });
    });


    // =====================================================================
    // Intercessor - Admin (admin.intercessor.*)
    // =====================================================================
    Route::prefix('intercessor')->name('admin.intercessor.')->group(function () {
        Route::get('/', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/relatorios', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorReportController::class, 'index'])->name('reports.index');
        Route::get('/configuracoes', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorSettingsController::class, 'index'])->name('settings.index');
        Route::match(['post', 'put'], '/configuracoes', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorSettingsController::class, 'update'])->name('settings.update');
        Route::resource('categories', \Modules\Intercessor\App\Http\Controllers\Admin\IntercessorCategoryController::class);
        Route::get('/moderacao', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorModerationController::class, 'index'])->name('moderation.index');
        Route::get('/moderacao/{request}', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorModerationController::class, 'show'])->name('moderation.show');
        Route::post('/moderacao/{request}/aprovar', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorModerationController::class, 'approve'])->name('moderation.approve');
        Route::post('/moderacao/{request}/rejeitar', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorModerationController::class, 'reject'])->name('moderation.reject');
        Route::post('/moderacao/{request}/comentarios', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorModerationController::class, 'storeComment'])->name('moderation.comment.store');
        Route::delete('/moderacao/{request}/comentarios/{interaction}', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorModerationController::class, 'destroyComment'])->name('moderation.comment.destroy');
        Route::delete('/moderacao/{request}', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorModerationController::class, 'destroy'])->name('moderation.destroy');
        Route::get('/moderacao-testemunhos', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorModerationController::class, 'indexTestimonies'])->name('moderation.testimonies.index');
        Route::post('/moderacao-testemunhos/{request}/aprovar', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorModerationController::class, 'approveTestimony'])->name('moderation.testimony.approve');
        Route::post('/moderacao-testemunhos/{request}/rejeitar', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorModerationController::class, 'rejectTestimony'])->name('moderation.testimony.reject');
        Route::prefix('equipe')->name('team.')->group(function () {
            Route::get('/', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorTeamController::class, 'index'])->name('index');
            Route::get('/adicionar', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorTeamController::class, 'create'])->name('create');
            Route::post('/', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorTeamController::class, 'store'])->name('store');
            Route::delete('/{user}', [\Modules\Intercessor\App\Http\Controllers\Admin\IntercessorTeamController::class, 'destroy'])->name('destroy');
        });
    });

    // =====================================================================
    // Worship - Admin (worship.admin.*)
    // =====================================================================
    Route::prefix('worship')->name('worship.admin.')->group(function () {
        Route::get('/', [\Modules\Worship\App\Http\Controllers\Admin\WorshipDashboardController::class, 'index'])->name('dashboard');
        Route::get('songs/import', [\Modules\Worship\App\Http\Controllers\Admin\SongImportController::class, 'showImportForm'])->name('songs.import');
        Route::post('songs/import-chordpro', [\Modules\Worship\App\Http\Controllers\Admin\SongImportController::class, 'importChordPro'])->name('songs.import-chordpro');
        Route::post('songs/import-opensong', [\Modules\Worship\App\Http\Controllers\Admin\SongImportController::class, 'importOpenSong'])->name('songs.import-opensong');
        Route::post('songs/reimport-bulk', [\Modules\Worship\App\Http\Controllers\Admin\SongImportController::class, 'reimportBulk'])->name('songs.reimport-bulk');
        Route::post('songs/{song}/reimport', [\Modules\Worship\App\Http\Controllers\Admin\SongImportController::class, 'reimport'])->name('songs.reimport');
        Route::resource('songs', \Modules\Worship\App\Http\Controllers\Admin\MusicLibraryController::class);
        Route::get('setlists', [\Modules\Worship\App\Http\Controllers\Admin\SetlistManagerController::class, 'index'])->name('setlists.index');
        Route::get('setlists/create', [\Modules\Worship\App\Http\Controllers\Admin\SetlistManagerController::class, 'create'])->name('setlists.create');
        Route::post('setlists', [\Modules\Worship\App\Http\Controllers\Admin\SetlistManagerController::class, 'store'])->name('setlists.store');
        Route::put('setlists/{setlist}', [\Modules\Worship\App\Http\Controllers\Admin\SetlistManagerController::class, 'update'])->name('setlists.update');
        Route::patch('setlists/{setlist}/status', [\Modules\Worship\App\Http\Controllers\Admin\SetlistManagerController::class, 'updateStatus'])->name('setlists.updateStatus');
        Route::get('setlists/{setlist}/manage', [\Modules\Worship\App\Http\Controllers\Admin\SetlistManagerController::class, 'manage'])->name('setlists.manage');
        Route::post('setlists/{setlist}/add-song', [\Modules\Worship\App\Http\Controllers\Admin\SetlistManagerController::class, 'addSong'])->name('setlists.addSong');
        Route::post('setlists/{setlist}/reorder', [\Modules\Worship\App\Http\Controllers\Admin\SetlistManagerController::class, 'reorder'])->name('setlists.reorder');
        Route::post('setlist-items/{item}/update', [\Modules\Worship\App\Http\Controllers\Admin\SetlistManagerController::class, 'updateItem'])->name('setlists.updateItem');
        Route::delete('setlist-items/{item}', [\Modules\Worship\App\Http\Controllers\Admin\SetlistManagerController::class, 'removeSong'])->name('setlists.removeSong');
        Route::delete('setlists/{setlist}', [\Modules\Worship\App\Http\Controllers\Admin\SetlistManagerController::class, 'destroy'])->name('setlists.destroy');
        Route::get('rosters', [\Modules\Worship\App\Http\Controllers\Admin\RosterController::class, 'index'])->name('rosters.index');
        Route::post('setlists/{setlist}/roster', [\Modules\Worship\App\Http\Controllers\Admin\RosterController::class, 'store'])->name('rosters.store');
        Route::delete('rosters/{roster}', [\Modules\Worship\App\Http\Controllers\Admin\RosterController::class, 'destroy'])->name('rosters.destroy');
        Route::resource('instruments', \Modules\Worship\App\Http\Controllers\Admin\WorshipInstrumentController::class)->names('instruments');
        Route::resource('categories', \Modules\Worship\App\Http\Controllers\Admin\InstrumentCategoryController::class)->names('categories');
        Route::get('api/assets', [\Modules\Worship\App\Http\Controllers\Admin\MediaAssetController::class, 'index'])->name('api.assets.index');
        Route::post('api/assets', [\Modules\Worship\App\Http\Controllers\Admin\MediaAssetController::class, 'store'])->name('api.assets.store');
        Route::get('academy', [\Modules\Worship\App\Http\Controllers\Admin\AcademyAdminController::class, 'index'])->name('academy.dashboard');
        Route::prefix('academy')->name('academy.')->group(function () {
            Route::get('courses', [\Modules\Worship\App\Http\Controllers\Admin\AcademyAdminController::class, 'coursesIndex'])->name('courses.index');
            Route::get('courses/create', [\Modules\Worship\App\Http\Controllers\Admin\AcademyAdminController::class, 'coursesCreate'])->name('courses.create');
            Route::post('courses', [\Modules\Worship\App\Http\Controllers\Admin\AcademyAdminController::class, 'coursesStore'])->name('courses.store');
            Route::get('courses/{id}', [\Modules\Worship\App\Http\Controllers\Admin\AcademyAdminController::class, 'coursesShow'])->name('courses.show');
            Route::get('courses/{id}/edit', [\Modules\Worship\App\Http\Controllers\Admin\AcademyAdminController::class, 'coursesEdit'])->name('courses.edit');
            Route::put('courses/{id}', [\Modules\Worship\App\Http\Controllers\Admin\AcademyAdminController::class, 'coursesUpdate'])->name('courses.update');
            Route::delete('courses/{id}', [\Modules\Worship\App\Http\Controllers\Admin\AcademyAdminController::class, 'coursesDestroy'])->name('courses.destroy');
            Route::post('courses/{id}/lessons', [\Modules\Worship\App\Http\Controllers\Admin\AcademyAdminController::class, 'storeLesson'])->name('courses.storeLesson');
            Route::put('courses/{id}/lessons/{lessonId}', [\Modules\Worship\App\Http\Controllers\Admin\AcademyAdminController::class, 'updateLesson'])->name('courses.updateLesson');
            Route::delete('courses/{id}/lessons/{lessonId}', [\Modules\Worship\App\Http\Controllers\Admin\AcademyAdminController::class, 'destroyLesson'])->name('courses.destroyLesson');
            Route::get('courses/{id}/builder', function ($id) {
                $course = \Modules\Worship\App\Models\AcademyCourse::findOrFail($id);

                return view('worship::admin.academy.dashboard', compact('course'));
            })->name('builder');
            Route::get('students', [\Modules\Worship\App\Http\Controllers\Admin\AcademyAdminController::class, 'students'])->name('students');
            Route::get('courses/{id}/wizard', [\Modules\Worship\App\Http\Controllers\Admin\AcademyAdminController::class, 'wizardStep2'])->name('courses.wizard');
            Route::post('courses/{id}/wizard', [\Modules\Worship\App\Http\Controllers\Admin\AcademyAdminController::class, 'wizardStep2Store'])->name('courses.wizard.store');
        });
        Route::get('setlists/{setlist}/print', [\Modules\Worship\App\Http\Controllers\Admin\SetlistManagerController::class, 'print'])->name('setlists.print');
        Route::get('setlists/{setlist}/print-roster', [\Modules\Worship\App\Http\Controllers\Admin\RosterController::class, 'print'])->name('rosters.print');
    });

}); // fim middleware admin
