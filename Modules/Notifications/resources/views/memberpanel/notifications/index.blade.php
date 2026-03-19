@extends('memberpanel::components.layouts.master')

@section('page-title', 'Notificações')

@section('content')
<div class="space-y-8 pb-12" x-data="memberNotificationsPage()" x-init="init()">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-slate-900 rounded-3xl shadow-2xl border border-slate-800">
        <!-- Decorative Mesh Gradient Background -->
        <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-pink-600 rounded-full blur-[100px]"></div>
            <div class="absolute top-1/2 right-10 w-80 h-80 bg-red-600 rounded-full blur-[100px]"></div>
        </div>

        <div class="relative px-8 py-10 flex flex-col md:flex-row items-center justify-between gap-8 z-10">
            <div class="flex-1 text-center md:text-left space-y-2">
                <p class="text-pink-200/80 font-bold uppercase tracking-widest text-xs">Atualizações</p>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                    Notificações
                </h1>
                <p class="text-slate-300 font-medium max-w-xl">
                    Fique por dentro de todas as novidades, avisos e atividades recentes.
                </p>
            </div>

            <div class="shrink-0 flex flex-wrap items-center gap-3 justify-center">
                <button type="button"
                    x-show="unreadCount > 0"
                    @click="markAllRead"
                    data-tour="notifications-read-all"
                    class="inline-flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-xl font-bold transition-all backdrop-blur-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    <x-icon name="check-circle" class="w-5 h-5 mr-2" />
                    Marcar todas como lidas
                </button>
                <button type="button"
                    x-show="meta.total > 0"
                    @click="clearAll"
                    class="inline-flex items-center px-6 py-3 bg-red-500/20 hover:bg-red-500/30 text-white border border-red-400/30 rounded-xl font-bold transition-all backdrop-blur-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    <x-icon name="trash" class="w-5 h-5 mr-2" />
                    Excluir todas
                </button>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="space-y-4 max-w-4xl mx-auto" data-tour="notifications-list">
        <template x-if="loading">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                <p class="text-gray-500 dark:text-gray-400">Carregando notificações...</p>
            </div>
        </template>

        <template x-for="item in notifications" :key="item.id">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 relative overflow-hidden group transition-all duration-300 hover:shadow-md border-l-4"
                 :class="item.is_read ? 'border-l-transparent' : 'ring-2 ring-blue-500/20 dark:ring-blue-500/40 border-l-blue-500'">
                <div class="absolute top-4 right-4 w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse shadow-lg shadow-blue-500/50" x-show="!item.is_read"></div>
                <div class="flex items-start gap-5">
                    <div class="shrink-0">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                             :class="item.notification?.type === 'success' ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' :
                                    (item.notification?.type === 'warning' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' :
                                    (item.notification?.type === 'error' ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' :
                                    (item.notification?.type === 'achievement' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400' :
                                    'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400')))">
                            <x-icon name="bell" class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="flex-1 min-w-0 pt-1">
                        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-1 pr-6" x-text="item.notification?.title ?? 'Notificação'"></h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-3" x-text="item.notification?.message ?? ''"></p>
                        <div class="flex flex-wrap items-center gap-4 mt-4">
                            <div class="flex items-center text-xs font-bold text-gray-400 uppercase tracking-wide">
                                <x-icon name="clock" class="w-3.5 h-3.5 mr-1" />
                                <span x-text="relativeTime(item.notification?.created_at)"></span>
                            </div>
                            <a x-show="item.notification?.action_url" :href="item.notification?.action_url"
                               class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg text-xs font-bold text-gray-700 dark:text-white transition-colors">
                                <span x-text="item.notification?.action_text || 'Ver detalhes'"></span>
                                <x-icon name="arrow-right" class="w-3 h-3 ml-2" />
                            </a>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <button type="button" x-show="!item.is_read" @click="markRead(item.id)"
                            class="p-2 text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20" title="Marcar como lida">
                            <x-icon name="check" class="w-5 h-5" />
                        </button>
                        <button type="button" @click="removeItem(item.id)"
                            class="p-2 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20" title="Remover">
                            <x-icon name="trash" class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <template x-if="!loading && notifications.length === 0">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <x-icon name="bell-off" class="w-10 h-10 text-gray-400" />
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Tudo limpo por aqui!</h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">Você não possui novas notificações no momento.</p>
            </div>
        </template>
    </div>

    <div class="max-w-4xl mx-auto mt-8" x-show="meta.last_page > 1">
        <div class="flex items-center justify-center gap-3">
            <button type="button" @click="goToPage(meta.current_page - 1)" :disabled="meta.current_page <= 1"
                class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-sm font-bold text-gray-700 dark:text-gray-200 disabled:opacity-40">
                Anterior
            </button>
            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                Página <span x-text="meta.current_page"></span> de <span x-text="meta.last_page"></span>
            </span>
            <button type="button" @click="goToPage(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page"
                class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-sm font-bold text-gray-700 dark:text-gray-200 disabled:opacity-40">
                Próxima
            </button>
        </div>
    </div>
</div>

<script>
function memberNotificationsPage() {
    return {
        loading: false,
        notifications: [],
        unreadCount: 0,
        meta: { current_page: 1, last_page: 1, total: 0, per_page: 15 },
        async init() {
            await this.loadPage(1);
            await this.syncUnreadCount();
        },
        async syncUnreadCount() {
            if (!window.NotificationSystem?.loadUnread) return;
            await window.NotificationSystem.loadUnread();
            const badge = document.getElementById('notification-badge');
            const text = badge?.textContent?.trim() || '0';
            this.unreadCount = text === '99+' ? 99 : Number(text || 0);
        },
        relativeTime(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            const diff = Math.floor((Date.now() - d.getTime()) / 1000);
            if (diff < 60) return 'agora';
            const min = Math.floor(diff / 60);
            if (min < 60) return `há ${min} min`;
            const hrs = Math.floor(min / 60);
            if (hrs < 24) return `há ${hrs} h`;
            const days = Math.floor(hrs / 24);
            return days === 1 ? 'ontem' : `há ${days} dias`;
        },
        async loadPage(page) {
            this.loading = true;
            const result = await window.NotificationSystem.fetchPage(page, this.meta.per_page || 15);
            if (result.success) {
                this.notifications = result.items;
                this.meta = result.meta;
            }
            this.loading = false;
        },
        async goToPage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            await this.loadPage(page);
        },
        async markRead(id) {
            const res = await window.NotificationSystem.markAsRead(id);
            if (!res.success) return;
            const item = this.notifications.find((n) => n.id === id);
            if (item) item.is_read = true;
            await this.syncUnreadCount();
        },
        async removeItem(id) {
            if (!confirm('Tem certeza que deseja remover esta notificação?')) return;
            const res = await window.NotificationSystem.delete(id);
            if (!res.success) return;
            this.notifications = this.notifications.filter((n) => n.id !== id);
            this.meta.total = Math.max(0, this.meta.total - 1);
            if (this.notifications.length === 0 && this.meta.current_page > 1) {
                await this.loadPage(this.meta.current_page - 1);
            }
            await this.syncUnreadCount();
        },
        async markAllRead() {
            const res = await window.NotificationSystem.markAllAsRead();
            if (!res.success) return;
            this.notifications = this.notifications.map((n) => ({ ...n, is_read: true }));
            this.unreadCount = 0;
        },
        async clearAll() {
            if (!confirm('Tem certeza que deseja excluir todas as notificações? Esta ação não pode ser desfeita.')) return;
            const res = await window.NotificationSystem.clearAll();
            if (!res.success) return;
            await this.loadPage(1);
            this.unreadCount = 0;
        },
    };
}
</script>
@endsection

