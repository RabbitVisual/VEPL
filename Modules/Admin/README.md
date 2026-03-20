# Módulo Admin – Visão Geral e Configurações do Sistema

O modulo **Admin** e o **nucleo de administracao** do VEPL Vertex Escola de Pastores e Lideres. Ele concentra dashboard, usuarios, permissoes, configuracoes globais do sistema, perfil do administrador (incluindo 2FA), HomePage, Biblia, CEP, notificacoes e integracao com os demais modulos.

Este documento descreve **como o módulo funciona**, em especial as **Configurações do Sistema**, o ciclo de vida global, cache, 2FA e as melhorias de engenharia e segurança aplicadas.

---

## 1. Escopo do Módulo

- **Dashboard** – Visão geral e atalhos.
- **Usuários** – CRUD, importação, roles (Spatie), perfil e **2FA (TOTP)**.
- **Configurações do Sistema** – Todas as configurações globais (Geral, Aparência, Segurança, Pagamentos, E-mail, Notificações, Sistema, Bíblia).
- **Módulos** – Ativar/desativar módulos (acesso técnico).
- **HomePage** – Carrossel, contatos, newsletter, configurações da vitrine.
- **Bíblia** – Planos de leitura e importação de versões.
- **CEP** – Faixas de CEP para lógica regional.
- **Recuperação de senha** – Configurações e histórico de resets.
- **Notificações** – Inbox e controle (engine em `Modules/Notifications`).
- **Manutenção** – Ativar/desativar modo manutenção com bypass para admin.

Rotas admin estão centralizadas em **`routes/admin.php`** (prefixo `admin`, middlewares `auth`, `verified`, `admin`). Configurações e 2FA exigem ainda o middleware **EnsureUserIsTechnicalAdmin** (quando aplicável).

---

## 2. Configurações do Sistema (Settings)

### 2.1. Onde fica

- **Menu:** Admin → **Configurações** (sidebar).
- **Rota:** `admin.settings.index` → `GET /admin/settings`.
- **Controller:** `Modules\Admin\App\Http\Controllers\SettingsController`.
- **View:** `Modules/Admin/resources/views/settings/index.blade.php`.

A tela é organizada em **abas**: Geral, Aparência, Segurança, Pagamentos, E-mail, Notificações, Sistema, Bíblia. O formulário único (`#settingsForm`) envia para `PUT /admin/settings` com `active_tab` para reabrir na aba atual após salvar.

### 2.2. Modelo e Cache (Performance)

- **Modelo:** `App\Models\Settings` (tabela `settings`: `key`, `value`, `type`, `group`, `description`).
- **Leitura:** `Settings::get($key, $default)` usa **cache em memória** com TTL de **24 horas** (`Settings::CACHE_TTL_SECONDS = 86400`). Cada chave é cacheada separadamente (`setting_{$key}`).
- **Gravação:** `Settings::set()` e `Settings::setMany()` fazem **Cache Buster**: ao salvar, limpam o cache da chave afetada (`Cache::forget("setting_{$key}")`) para que as mudanças reflitam **imediatamente**.
- **Limpeza global:** `Settings::clearCache()` percorre todas as chaves da tabela e faz `Cache::forget` em cada uma. É chamado no `SettingsController::update()` após persistir todas as alterações.

Assim, o sistema obtém **performance** (menos leituras no banco) e **consistência** (após salvar no Admin, a próxima requisição já usa os novos valores).

### 2.3. Ciclo de Vida Global (AppServiceProvider)

No **`AppServiceProvider::boot()`**, o sistema chama **`SettingsHelper::applyGlobalSettings()`** no início de cada requisição. Esse método lê as configurações do banco (usando o cache quando existir) e aplica em tempo de execução:

- **Broadcasting/Pusher** – driver e opções.
- **Mail** – driver, SMTP, remetente, SES, Mailgun.
- **Cache e Session** – drivers e lifetime.
- **Queue** – conexão padrão.
- **App** – `name`, **timezone**, **locale**, `date_format`, `time_format` (formatos de data/hora para logs, e-mails e UI).
- **reCAPTCHA** – `services.recaptcha` (enabled, version, v3_score_threshold, keys).
- **2FA** – `auth.2fa.enabled` e `auth.2fa.provider` (usados no login para exigir código TOTP).

Isso garante **consistência** de timezone, idioma e formatos em todo o sistema (logs, e-mails, interfaces) sem depender apenas do `.env`.

### 2.4. Validação (Form Request)

Toda a validação do formulário de configurações foi movida para **`Modules\Admin\App\Http\Requests\UpdateSettingsRequest`**. O controller **não** chama `$request->validate()`; ele apenas:

1. Recebe o **UpdateSettingsRequest** (já validado).
2. Usa **`$request->validated()`** para obter os dados.
3. Chama **`Settings::set(...)`** para cada chave (Geral, Aparência, Segurança, E-mail, etc.).
4. Chama **`Settings::clearCache()`** e **`SettingsHelper::applyGlobalSettings()`**.
5. Executa `config:clear` e `cache:clear`.
6. Redireciona com mensagem de sucesso: **"Configurações Globais Aplicadas e Cache Atualizado."**

Assim, o controller fica focado em **orquestração** e o Request concentra as **regras de validação** (tipos, tamanhos, opções permitidas).

### 2.5. Abas Resumidas

| Aba              | Conteúdo principal                                                                                                    |
| ---------------- | --------------------------------------------------------------------------------------------------------------------- |
| **Geral**        | Nome do site, descrição, e-mail, telefone, endereço; timezone, locale, primeiro dia da semana, formatos de data/hora. |
| **Aparência**    | Logo, ícone/favicon.                                                                                                  |
| **Segurança**    | reCAPTCHA (habilitar, v2/v3, score mínimo v3, chaves); **2FA** (habilitar para admins, provedor Google/Microsoft).    |
| **Pagamentos**   | Link/atalho para gateways (módulo PaymentGateway).                                                                    |
| **E-mail**       | Mailer (SMTP, SES, Mailgun, etc.), host, porta, remetente, chaves SES/Mailgun; teste de envio.                        |
| **Notificações** | Configurações de broadcasting/notificações.                                                                           |
| **Sistema**      | Cache, sessão, fila; botões de manutenção (ativar/desativar).                                                         |
| **Bíblia**       | Versão padrão (sigla); fallback: sigla → default → primeiro ativo (integrado ao cache).                               |

### 2.6. UI/UX nas Configurações

- **Loading overlay:** O formulário `#settingsForm` dispara `<x-loading-overlay />` no submit (mensagem "Salvando configurações...").
- **reCAPTCHA v3 – Score:** O campo "Score mínimo (v3)" fica visível **somente** quando a versão selecionada é **v3** (script `toggleRecaptchaV3Score()` no carregamento e no `change` do select).
- **Toast:** Após salvar com sucesso, além do alert no topo da página, um **toast** é exibido no canto inferior direito (`#notification-toast-container`) com a mensagem **"Configurações Globais Aplicadas e Cache Atualizado."** (auto-dismiss em 5 segundos).

---

## 3. Segurança: 2FA (TOTP)

### 3.1. Visão geral

- **Configuração global:** Em **Configurações → Segurança**, o administrador pode marcar **"Habilitar 2FA (TOTP) para administradores"** e escolher o **Provedor 2FA** (Google ou Microsoft Authenticator). Ambos usam o mesmo padrão TOTP (RFC 6238).
- **Por usuário:** Cada admin que for obrigado a usar 2FA precisa **ativar** no próprio perfil: **Meu Perfil → Autenticação em duas etapas (2FA)** (`admin.profile.2fa.show`). Lá ele gera a secret, escaneia o QR Code no app e confirma com um código de 6 dígitos.

### 3.2. Fluxo de ativação (perfil admin)

1. Admin acessa **Perfil → Autenticação em duas etapas (2FA)** (ou pelo link na seção "Dados do Sistema" do perfil, quando 2FA global está ativo).
2. Clica em **"Ativar 2FA (mostrar QR Code)"** → `POST admin/profile/2fa/setup`. O sistema gera uma **secret** TOTP, armazena na sessão (`2fa_setup_secret`) e redireciona de volta para a mesma página.
3. A página exibe o **QR Code** (gerado com `pragmarx/google2fa` + `simplesoftwareio/simple-qrcode`) e um campo para o **código de 6 dígitos**.
4. O usuário escaneia com Google ou Microsoft Authenticator e informa o código → `POST admin/profile/2fa/confirm`. O serviço **TwoFactorAuthService** verifica o código; se válido, persiste a secret no usuário (`two_factor_secret`, criptografado) e define `two_factor_confirmed_at`. A sessão de setup é limpa.
5. Para **desativar** 2FA, o usuário clica em "Desativar 2FA" e confirma com a **senha atual** → `POST admin/profile/2fa/disable`.

### 3.3. Fluxo no login

1. Usuário informa e-mail/CPF e senha no login público.
2. **LoginController** valida reCAPTCHA (se ativo) e credenciais. Se a senha estiver correta e o usuário for **admin** com **2FA ativo globalmente** (`config('auth.2fa.enabled')`) e **2FA configurado no usuário** (`hasTwoFactorEnabled()`), o sistema **não** faz login ainda: grava na sessão `login.id` e `login.remember` e redireciona para **`/login/2fa`** (formulário de código).
3. Na tela de 2FA, o usuário digita o **código de 6 dígitos** do app e envia → `POST /login/2fa` (`login.2fa.verify`). O **TwoFactorAuthService** verifica o código contra a secret do usuário; se válido, o login é concluído (`Auth::login($user, $remember)`), a sessão de 2FA é limpa e o usuário é redirecionado para o dashboard admin.

### 3.4. Implementação técnica

- **Pacote:** `pragmarx/google2fa` (geração de secret, URL otpauth, verificação do código).
- **QR Code:** `SimpleSoftwareIO\QrCode` (PNG) a partir da URL otpauth; exibido na view como base64 ou via rota `admin/profile/2fa/qr` (lê secret da sessão).
- **Banco:** tabela `users` – colunas `two_factor_secret` (texto, criptografado via cast `encrypted`) e `two_factor_confirmed_at` (timestamp). Migração: `2026_03_07_120000_add_two_factor_to_users_table.php`.
- **Serviço:** `App\Services\TwoFactorAuthService` (injetável; usa `Google2FA` para gerar secret, obter URL, verificar código; métodos `enableForUser` e `disableForUser`).
- **Controller Admin:** `Modules\Admin\App\Http\Controllers\TwoFactorController` (show, setup, confirm, disable, qrImage).
- **Rotas:** `admin.profile.2fa.show`, `admin.profile.2fa.setup`, `admin.profile.2fa.confirm`, `admin.profile.2fa.disable`, `admin.profile.2fa.qr`.
- **Rotas login:** `login.2fa.form` (`GET /login/2fa`), `login.2fa.verify` (`POST /login/2fa`).
- **Modelo User:** método `hasTwoFactorEnabled()` (true se `two_factor_secret` preenchido e `two_factor_confirmed_at` não nulo). A secret fica em `$hidden` para não serializar em JSON/API.

---

## 4. Melhorias Implementadas (Resumo)

| Melhoria                      | Descrição                                                                                                                              |
| ----------------------------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| **Cache 24h**                 | `Settings::get()` usa TTL de 24 horas; constante `Settings::CACHE_TTL_SECONDS`.                                                        |
| **Cache Buster**              | Em `Settings::set()`/`setMany()` e em `clearCache()`; chamado após salvar configurações no Admin.                                      |
| **Ciclo de vida global**      | `AppServiceProvider::boot()` chama `SettingsHelper::applyGlobalSettings()` para timezone, locale, formatos, mail, recaptcha, 2FA, etc. |
| **2FA TOTP real**             | Remoção do "em breve"; fluxo completo com QR, confirmação e desafio no login.                                                          |
| **UpdateSettingsRequest**     | Toda validação do formulário de configurações concentrada no Form Request; controller só orquestra.                                    |
| **Toast pós-salvar**          | Mensagem "Configurações Globais Aplicadas e Cache Atualizado." em toast (e alert no topo).                                             |
| **Toggle score reCAPTCHA v3** | Campo de score mínimo visível apenas quando a versão reCAPTCHA é v3.                                                                   |
| **Loading overlay**           | Formulário de configurações e fluxos 2FA disparam o overlay no submit.                                                                 |

---

## 5. Arquivos Principais (Referência Rápida)

- **Configurações:** `SettingsController`, `UpdateSettingsRequest`, `settings/index.blade.php`.
- **2FA:** `TwoFactorController`, `profile/two-factor.blade.php`, `App\Services\TwoFactorAuthService`, `LoginController` (métodos `showTwoFactorForm`, `verifyTwoFactor`).
- **Global:** `App\Models\Settings`, `App\Helpers\SettingsHelper`, `App\Providers\AppServiceProvider`.
- **Login 2FA (view):** `Modules/HomePage/resources/views/auth/login-2fa.blade.php`.

Documentação alinhada ao padrão do projeto e ao refinamento de engenharia e segurança máxima (VEPL Escola).
