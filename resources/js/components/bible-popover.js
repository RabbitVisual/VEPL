/**
 * Popover global para citações `.bible-reference-link` (painel de membros).
 * Consome GET /api/v1/bible/context
 */
export default () => ({
    show: false,
    loading: false,
    error: null,
    data: null,
    tab: 'text',
    pos: { top: '0px', left: '0px' },
    centered: false,
    hideTimer: null,
    cache: new Map(),
    isTouch: false,

    init() {
        this.isTouch =
            'ontouchstart' in window || (navigator.maxTouchPoints ?? 0) > 0;

        document.addEventListener(
            'mouseover',
            (e) => {
                if (this.isTouch) {
                    return;
                }
                const el = e.target?.closest?.('.bible-reference-link');
                if (!el) {
                    return;
                }
                this.clearHide();
                this.openFor(el);
            },
            true
        );

        document.addEventListener(
            'mouseout',
            (e) => {
                if (this.isTouch) {
                    return;
                }
                const el = e.target?.closest?.('.bible-reference-link');
                if (!el) {
                    return;
                }
                this.scheduleHide();
            },
            true
        );

        document.addEventListener(
            'click',
            (e) => {
                const el = e.target?.closest?.('.bible-reference-link');
                if (el) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.clearHide();
                    this.openFor(el);
                    return;
                }
                if (
                    !e.target?.closest?.('.bible-reference-popover') &&
                    !e.target?.closest?.('.bible-reference-link')
                ) {
                    this.show = false;
                }
            },
            true
        );

        window.addEventListener('open-bible-modal', (e) => {
            const ref = e.detail?.ref;
            if (!ref) {
                return;
            }
            this.centered = true;
            this.show = true;
            this.tab = 'text';
            this.loadRef(String(ref));
        });

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.close();
            }
        });
    },

    onPopoverEnter() {
        this.clearHide();
    },

    onPopoverLeave() {
        if (!this.isTouch) {
            this.scheduleHide();
        }
    },

    clearHide() {
        if (this.hideTimer) {
            clearTimeout(this.hideTimer);
            this.hideTimer = null;
        }
    },

    scheduleHide() {
        this.clearHide();
        this.hideTimer = setTimeout(() => {
            this.show = false;
        }, 180);
    },

    positionFromEl(el) {
        const r = el.getBoundingClientRect();
        const margin = 8;
        const width = Math.min(420, window.innerWidth - 24);
        let left = r.left;
        if (left + width > window.innerWidth - 12) {
            left = Math.max(12, window.innerWidth - width - 12);
        }
        this.pos = {
            top: `${r.bottom + margin}px`,
            left: `${left}px`,
        };
    },

    async openFor(el) {
        const ref = el.dataset?.reference;
        if (!ref) {
            return;
        }
        this.centered = false;
        this.positionFromEl(el);
        this.show = true;
        this.tab = 'text';
        await this.loadRef(ref);
    },

    async loadRef(ref) {
        if (this.cache.has(ref)) {
            this.data = this.cache.get(ref);
            this.error = null;
            this.loading = false;

            return;
        }
        this.loading = true;
        this.error = null;
        this.data = null;
        try {
            const url = `/api/v1/bible/context?ref=${encodeURIComponent(ref)}`;
            const r = await fetch(url, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            const json = await r.json();
            if (!r.ok) {
                throw new Error(json.message || 'Não foi possível carregar o contexto.');
            }
            this.data = json.data;
            this.cache.set(ref, json.data);
        } catch (err) {
            this.error = err?.message || 'Erro ao carregar.';
        } finally {
            this.loading = false;
        }
    },

    close() {
        this.show = false;
        this.centered = false;
    },
});
