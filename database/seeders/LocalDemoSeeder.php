<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocalDemoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Dados de demonstração para ambiente local — apenas módulos ativos.
     *
     * Módulos removidos (NÃO incluir): EBD, Gamification, ChurchCouncil,
     * SocialAction, Assets, Projection, Marketplace, Treasury.
     */
    public function run(): void
    {
        $this->call([
            // ── Conteúdo do site / home ──────────────────────────────────────
            \Modules\HomePage\Database\Seeders\HomePageDatabaseSeeder::class,

            // ── Bíblia e estudo ──────────────────────────────────────────────
            \Modules\Bible\Database\Seeders\BibleDatabaseSeeder::class,

            // ── Louvor e Música ──────────────────────────────────────────────
            \Modules\Worship\Database\Seeders\WorshipDatabaseSeeder::class,

            // ── Intercessão ──────────────────────────────────────────────────
            \Modules\Intercessor\Database\Seeders\IntercessorDatabaseSeeder::class,

            // ── Eventos ──────────────────────────────────────────────────────
            \Modules\Events\Database\Seeders\EventsDatabaseSeeder::class,

            // ── Pregações ────────────────────────────────────────────────────
            \Modules\Sermons\Database\Seeders\SermonsDatabaseSeeder::class,

            // ── Ministérios ──────────────────────────────────────────────────
            \Modules\Ministries\Database\Seeders\MinistriesDatabaseSeeder::class,

            // ── Painel do Membro ─────────────────────────────────────────────
            \Modules\MemberPanel\Database\Seeders\MemberPanelDatabaseSeeder::class,

            // ── Administração ────────────────────────────────────────────────
            \Modules\Admin\Database\Seeders\AdminDatabaseSeeder::class,

            // ── Notificações ─────────────────────────────────────────────────
            \Modules\Notifications\Database\Seeders\NotificationsDatabaseSeeder::class,
        ]);
    }
}
