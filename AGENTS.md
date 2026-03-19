# VEPL Project - Agentic Context (Master Guide for Jules)

Welcome, Jules! This project is the **Escola de Pastores e Líderes (VEPL)**. It is a premium management ecosystem for Church Leadership, Academy, and Worship.

## 🎯 Mandatory Development Philosophy
Your goal is to maintain the **"Vertex Standard"**: a premium, consistent, and high-performance user experience.

1.  **Local & Internal First**:
    - **NEVER** use external APIs or CDNs. All assets (CSS, JS, Fonts, Icons) MUST be local.
    - **Bible Resources**: ALWAYS use `Modules/Bible`. It is our scripture source of truth.
    - **Worship Data**: Use `Modules/Worship` for chords, setlists, and academy content.
2.  **Low-Cost Live Features**:
    - Use Alpine.js, Livewire Polling, or Vanilla JS for "live" updates. Avoid heavy infrastructure like WebSockets unless strictly necessary.
3.  **Premium Aesthetic**: Didactic, glassmorphic, and clean "book-like" design. Professionalism is non-negotiable.

## 📂 Comprehensive Module Map
- **Admin**: System brain. Users, RBAC (Spatie), and global settings.
- **Academy (EBD)**: The Vertex Academy. Gamified LMS for theological formation.
- **Worship**: **Core Liturgy.** Manage setlists, musicians, and the Levite Academy (Masterclasses).
- **Bible**: Spiritual engine. Multi-version offline scriptures.
- **Sermons**: Educational media repository and homiletics database.
- **Events**: Public engagement and registration lifecycle.
- **Treasury**: Auditable financial governance and church bookkeeping.
- **Ministries**: Departmental organization (Youth, Women, etc.).
- **SocialAction**: Outreach and logistics for community support.
- **Intercessor**: Moderated prayer network and commitment tracking.
- **Projection**: Real-time service media (Lyrics, Bible, Media) via JS-Screen.
- **MemberPanel**: Personalized member portal and profile management.

## 🎨 UI Standards (FontAwesome 7.1 Pro)
- **Icons**: Use ONLY **Font Awesome 7.1 Pro Duotone** via `<x-icon name="..." />`.
- **Branding**: Colors follow the HSL palette defined in `resources/css/app.css` (Tailwind 4.1).
- **Loading**: Use `<x-loading-overlay />` for full-page transitions and form submissions.

## 🛠 Setup & Governance
- **Rebranding**: The project is now **VEPL**. Remove any legacy references to "VertexCBAV".
- **Migrations**: Always consolidate related changes into the module's core migration when possible to avoid clutter.
- **Seeders**: Keep them idempotent. Use `updateOrCreate` to allow safe re-running.

Go ahead, Jules, and keep making VEPL the most professional church leadership tool on the market!
