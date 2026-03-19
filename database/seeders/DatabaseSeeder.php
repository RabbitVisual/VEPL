<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Seeders de produção: idempotentes e seguros para re-executar.
     * Seeders de demo: executados apenas em ambiente local/dev.
     */
    public function run(): void
    {
        // ── Usuários de demonstração (apenas local) ────────────────────────────
        if (app()->environment('local', 'development', 'dev')) {
            $this->call(DemoUsersSeeder::class);
        }

        // ── Módulos de produção (idempotentes) ─────────────────────────────────
        $this->call([
            \Modules\PaymentGateway\Database\Seeders\PaymentGatewayDatabaseSeeder::class,
            \Modules\Notifications\Database\Seeders\NotificationTemplatesSeeder::class,
            \Modules\Intercessor\Database\Seeders\IntercessorDatabaseSeeder::class,
        ]);

        // ── Dados de demonstração por módulo (apenas local) ───────────────────
        if (app()->environment('local', 'development', 'dev')) {
            $this->call(LocalDemoSeeder::class);
        }
    }
}
