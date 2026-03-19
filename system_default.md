# System Architecture & Development Standards - VEPL

## 1. Local-First & Internal Resource Philosophy
The application is designed to be **entirely self-contained**.
- **No External CDNs**: All assets must be served from `public/`.
- **Bible Data**: Use `Modules/Bible`. Offline versions are bundled.
- **Worship Resources**: Use `Modules/Worship` for the repertoire and liturgy.

## 2. Real-Time & Live (Low-Cost)
- **Avoid High-Cost Infrastructure**: Stick to Alpine.js and polling for real-time effects.
- **Scalability**: Clean JS logic that allows future WebSocket implementation if the organization scales.

## 3. Core Technology Stack
- **Backend**: Laravel 12.x (PHP 8.2+)
- **Frontend**: Alpine.js v3 & Blade Templates
- **Styling**: **Tailwind CSS v4.1** (Local/Vite)
- **Architecture**: Modular Monolith (`nwidart/laravel-modules`)

## 4. Iconography & Fonts (100% Local)
O sistema opera em modo **Offline-Ready**.
- **Fonts**: Inter (Sans) and Poppins (Display) are self-hosted.
- **Icons**: **Font Awesome 7.1 Pro** is the primary visual language.
- **Zero CDN Policy**: No external Kits or CSS links. Use `<x-icon />`.

## 5. UI Patterns
- **Standard**: Follow the **"Vertex Premium"** design system.
- **Feedback**: Standardized loading overlays, skeletons, and error pages.

## 6. Module Map
1. **Admin**: Governance & Users.
2. **Academy**: Theological Education.
3. **Worship**: Music & Musician Training.
4. **Bible**: Sacred Text Engine.
5. **Sermons**: Media & Content.
6. **Events**: Logistics & Community.
7. **Treasury**: Financial Accountability.
8. **Ministries**: Org Sections.
9. **SocialAction**: Charity.
10. **Projection**: Live Media.
11. **Intercessor**: Prayer Network.
12. **MemberPanel**: Citizen Portal.

## 7. Development Workflow
- **Vite**: Keep the dev server running for Tailwind 4 compilation.
- **Modules**: Respect the separation of concerns. Do not cross-link modules unnecessarily; use Service classes for communication.

© 2026 Vertex Solutions LTDA.