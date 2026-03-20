# 📦 Consolidação e Upgrade do Módulo Ministries para VEPL

## Principais Mudanças

- **Migrations Consolidadas**
  - De 6 migrations antigas fragmentadas para **4 novas migrations robustas**:
    - `2026_01_01_300001_create_ministries_table.php`
    - `2026_01_01_300002_create_ministry_members_table.php`
    - `2026_01_01_300003_create_ministry_plans_and_reports_tables.php`
    - `2026_01_01_300004_create_ministry_service_points_table.php`
  - Remoção dos arquivos antigos de `Modules/Ministries/database/migrations`.

- **Fluxo Administrativo e Aprovação**
  - Novo fluxo com status administrativos (`under_admin_review`) e campos como `submitted_at`, `approval_notes`, etc.
  - _Backend_ **alinhado ao VEPL**: sem acoplamentos com modulos legados.

## Alterações em Código

- **Modelos**
  - `MinistryPlan.php`
    - Removido: `council_approval_id`, `councilApproval()`
    - Novo status: `STATUS_UNDER_ADMIN_REVIEW`
    - Casts atualizados
  - `MinistryReport.php`
    - Status atualizado para `under_admin_review`
  - `Ministry.php`
    - Removidos relacionamentos legados
- **Policies**
  - `MinistryPolicy.php`:
    - Removidas verificações para `councilMember()`
- **Controllers (Admin)**
  - `Admin/MinistryPlanController.php`:
    - Substituído fluxo `submitToCouncil` por **`submitForApproval`**
    - Removidas dependências de `CouncilApproval`/`CouncilAuditService`
    - Mensagens/status ajustados para aprovação administrativa
- **Services**
  - `MinistryPlanEventService.php`:
    - Removido acoplamento com `CouncilApproval`
    - Usa `requires_administrative_approval`
    - Usa status `Event::STATUS_AWAITING_APPROVAL` (compatível VEPL)
- **Controllers (MemberPanel)**
  - `Member/MinistryController.php`:
    - Aceitação de solicitação agora é **direta via `acceptRequest`** (sem conselho)
    - Mensagens adaptadas para linguagem VEPL
    - Protecao adicional para evitar dependencias legadas
  - `routes/member.php`:
    - Rota de aceite aponta para `acceptRequest`
- **Rotas Admin**
  - `routes/admin.php`:
    - Fluxo `submit-to-council` substituído por `submit-for-approval`
      (ex: `admin.ministries.plans.submit-for-approval`)

## Melhorias nas Views

- **Ajustes de texto e fluxo** (VEPL):
  - `resources/views/admin/index.blade.php`
  - `resources/views/admin/create.blade.php`
  - `resources/views/admin/plans/index.blade.php`
  - `resources/views/admin/plans/show.blade.php`
  - `resources/views/memberpanel/show.blade.php`
  - `resources/views/memberpanel/reports/form.blade.php`
- **Tailwind:**
  - Corrigido: `flex-shrink-0` → `shrink-0`
  - Corrigido: classes duplicadas na badge de status

## Seeder VEPL

- `database/seeders/MinistriesDatabaseSeeder.php`
  - Popula automaticamente ministérios VEPL (formação pastoral, discipulado, louvor/liturgia, missão/evangelização) via `updateOrCreate`

## Validação

- `php -l` em todos os arquivos alterados: ✅ OK
- `php artisan migrate --pretend --path="Modules/Ministries/database/migrations"`: ✅ OK (SQL correto das 4 novas migrations)
- ReadLints: ✅ OK (sem erros)
- Observação:
  - Ajustes para manter `php artisan route:list` estavel sem dependencias legadas
    (_não relacionado às mudanças do Ministries_)

---

> Se desejar, o próximo passo pode ser o ciclo runtime do módulo:
> - `php artisan migrate --path=Modules/Ministries/database/migrations`
> - `php artisan db:seed --class=Modules\Ministries\Database\Seeders\MinistriesDatabaseSeeder`
> - Smoke test nas telas **admin/member** de ministérios.

---
