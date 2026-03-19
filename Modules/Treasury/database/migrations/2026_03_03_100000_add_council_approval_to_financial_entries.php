<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Aprovação administrativa para lançamentos acima do limite de orçamento.
     */
    public function up(): void
    {
        Schema::table('financial_entries', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected', 'auto_approved'])->nullable()->after('metadata')->comment('Status de aprovação administrativa');
            $table->foreignId('approved_by')->nullable()->after('approval_status')->constrained('users')->nullOnDelete()->comment('Usuário que aprovou');
            $table->timestamp('approved_at')->nullable()->after('approved_by')->comment('Data da aprovação');
            $table->text('approval_notes')->nullable()->after('approved_at')->comment('Observações da aprovação');
            $table->decimal('approval_threshold_amount', 10, 2)->nullable()->after('approval_notes')->comment('Limite que acionou aprovação');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_entries', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approval_status', 'approved_by', 'approved_at', 'approval_notes', 'approval_threshold_amount']);
        });
    }
};
