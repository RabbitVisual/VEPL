<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Controle de Acesso Baseado em Papéis (RBAC).
     *
     * Inclui: roles, permissions, role_permission + seeds iniciais.
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // PAPÉIS (Roles)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['name' => 'Administrador', 'slug' => 'admin',  'description' => 'Acesso total ao sistema',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Membro',        'slug' => 'membro', 'description' => 'Aluno da Escola de Pastores', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // FK users → roles (após os dois creates)
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
        });

        // ─────────────────────────────────────────────────────────────────────
        // PERMISSÕES (Permissions)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('module')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        DB::table('permissions')->insert([
            ['name' => 'Ministério de Jovens',   'slug' => 'jovens',  'module' => 'Jovens',  'description' => 'Acesso à área de Jovens',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ministério de Crianças', 'slug' => 'criancas','module' => 'Crianças','description' => 'Acesso à área de Crianças','created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ministério de Mulheres', 'slug' => 'mulheres','module' => 'Mulheres','description' => 'Acesso à área de Mulheres','created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ministério de Homens',   'slug' => 'homens',  'module' => 'Homens',  'description' => 'Acesso à área de Homens',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ministério de Música',   'slug' => 'musica',  'module' => 'Música',  'description' => 'Acesso à área de Música',  'created_at' => now(), 'updated_at' => now()],
        ]);

        // ─────────────────────────────────────────────────────────────────────
        // PIVOT — role_permission
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
        });

        // Admin recebe todas as permissões automaticamente
        $adminId     = DB::table('roles')->where('slug', 'admin')->value('id');
        $permissions = DB::table('permissions')->pluck('id');

        foreach ($permissions as $permissionId) {
            DB::table('role_permission')->insert([
                'role_id'       => $adminId,
                'permission_id' => $permissionId,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });
        Schema::dropIfExists('roles');
    }
};
