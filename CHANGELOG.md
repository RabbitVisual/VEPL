# Changelog - VEPL

All notable changes to the **Escola de Pastores e Líderes (VEPL)** project will be documented in this file.

## [1.2.0] - 2026-03-19
### Added
- **Módulo Worship (Consolidado)**: Unificação de 23 migrações em um Schema Core profissional.
- **Worship Academy Masterclass**: Seeder profissional com cursos, lições em vídeo (multicam) e materiais de apoio (PDF/ChordPro).
- **Logística de Equipamentos**: Novo controle de inventário de som e instrumentos integrado às funções da equipe.
- **Categorização de Instrumentos**: Suporte a categorias (Harmonia, Percussão, Vocal, Técnico) com ícones e cores HSL.

### Changed
- **Rebranding Global**: Transição de "VertexCBAV" para "VEPL".
- **Refatoração de Modelos**: Unificação de `AcademyCourse`, `AcademyLesson` e `AcademyProgress` sob o novo padrão de dados.
- **Estabilização de Seeders**: Idempotência garantida em todos os seeders do módulo Worship.
- **Documentação Master**: Atualização do `README.md`, `AGENTS.md` e `system_default.md`.

### Fixed
- Erro de integridade referencial em `worship_equipment` durante o seeding.
- Conflito cronológico em migrações entre os módulos `Sermons` e `Worship`.
- Geração automática de UUIDs em notificações através da remoção da trava de eventos no seeder principal.

---

## [1.1.0] - 2026-02-28
### Added
- **Módulo Sermons (Estabilizado)**: Suporte a múltiplos anexos, tags e integração com repertório de músicas.
- **Módulo Bible**: Estabilização do motor de busca e seletores bíblicos globais.
- **Vertex Standard UI**: Padronização de ícones FontAwesome 7.1 Pro (Local) e fontes auto-hospedadas.

---

## [1.0.0] - 2026-01-15
### Initial Release
- Lançamento oficial da plataforma VEPL com arquitetura Monolito Modular.
- 18 módulos base integrados.
- Suporte a Gateway de Pagamentos (Mercado Pago, Stripe, PIX).
