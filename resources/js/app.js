/**
 * Perpusa front-end interactions.
 * Small, dependency-free enhancements: password visibility, flash messages,
 * mobile navigation, confirmations and the subtle page transition used across
 * the application.
 */

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ------------------------------------------------------------------ */
/* Password visibility                                                 */
/* ------------------------------------------------------------------ */

function initPasswordVisibility() {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = document.getElementById(button.dataset.passwordToggle);

            if (! target) {
                return;
            }

            const isPassword = target.type === 'password';
            target.type = isPassword ? 'text' : 'password';
            button.dataset.visible = isPassword ? 'true' : 'false';
            button.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    });
}

/* ------------------------------------------------------------------ */
/* Flash messages                                                      */
/* ------------------------------------------------------------------ */

function initFlashMessages() {
    document.querySelectorAll('[data-flash]').forEach((flash) => {
        const dismiss = () => {
            flash.classList.add('transition-out');

            window.setTimeout(() => flash.remove(), 200);
        };

        flash.querySelector('[data-flash-close]')?.addEventListener('click', dismiss);

        window.setTimeout(() => {
            if (document.body.contains(flash)) {
                dismiss();
            }
        }, 6000);
    });
}

/* ------------------------------------------------------------------ */
/* Mobile sidebar                                                      */
/* ------------------------------------------------------------------ */

function initSidebar() {
    const sidebar = document.querySelector('[data-sidebar]');
    const overlay = document.querySelector('[data-sidebar-overlay]');
    const openers = document.querySelectorAll('[data-sidebar-open]');
    const closers = document.querySelectorAll('[data-sidebar-close]');

    if (! sidebar) {
        return;
    }

    const setOpen = (open) => {
        sidebar.classList.toggle('-translate-x-full', ! open);
        overlay?.classList.toggle('hidden', ! open);
        document.body.classList.toggle('overflow-hidden', open);

        openers.forEach((button) => button.setAttribute('aria-expanded', open ? 'true' : 'false'));
    };

    openers.forEach((button) => button.addEventListener('click', () => setOpen(true)));
    closers.forEach((button) => button.addEventListener('click', () => setOpen(false)));

    sidebar.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setOpen(false)));

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            setOpen(false);
        }
    });
}

/* ------------------------------------------------------------------ */
/* Mobile navbar dropdown                                              */
/* ------------------------------------------------------------------ */

function initDropdownMenus() {
    const menus = Array.from(document.querySelectorAll('[data-menu-toggle]'))
        .map((button) => ({ button, panel: document.getElementById(button.dataset.menuToggle) }))
        .filter(({ panel }) => panel !== null);

    if (menus.length === 0) {
        return;
    }

    const closeMenus = (except = null) => {
        menus.forEach(({ button, panel }) => {
            if (panel === except) {
                return;
            }

            panel.classList.add('hidden');
            button.setAttribute('aria-expanded', 'false');
        });
    };

    menus.forEach(({ button, panel }) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();

            const shouldOpen = panel.classList.contains('hidden');
            closeMenus();

            if (shouldOpen) {
                panel.classList.remove('hidden');
                button.setAttribute('aria-expanded', 'true');
            }
        });
    });

    document.addEventListener('click', (event) => {
        if (menus.some(({ button, panel }) => button.contains(event.target) || panel.contains(event.target))) {
            return;
        }

        closeMenus();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenus();
        }
    });
}

/* ------------------------------------------------------------------ */
/* Destructive action confirmations                                    */
/* ------------------------------------------------------------------ */

function initConfirmations() {
    document.querySelectorAll('[data-confirm]').forEach((element) => {
        element.addEventListener('click', (event) => {
            if (! window.confirm(element.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (! window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });
}

/* ------------------------------------------------------------------ */
/* Page transitions                                                    */
/* ------------------------------------------------------------------ */

function initPageTransitions() {
    if (prefersReducedMotion) {
        return;
    }

    const body = document.body;

    window.addEventListener('pageshow', () => {
        body.classList.remove('transition-out');
        body.classList.remove('opacity-0');
    });

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a');

        if (! link || event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        if (link.target === '_blank' || link.hasAttribute('download')) {
            return;
        }

        const href = link.getAttribute('href');

        if (! href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) {
            return;
        }

        const url = new URL(link.href, window.location.href);

        if (url.origin !== window.location.origin) {
            return;
        }

        if (url.pathname === window.location.pathname && url.search === window.location.search) {
            return;
        }

        if (link.dataset.noTransition !== undefined) {
            return;
        }

        event.preventDefault();
        body.classList.add('transition-out');

        window.setTimeout(() => {
            window.location.href = link.href;
        }, 160);
    });

    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', () => {
            if (! form.hasAttribute('data-confirm')) {
                body.classList.add('transition-out');
            }
        });
    });
}

/* ------------------------------------------------------------------ */
/* Copy to clipboard                                                   */
/* ------------------------------------------------------------------ */

function initCopyButtons() {
    document.querySelectorAll('[data-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
            } catch {
                return;
            }

            const original = button.textContent;
            button.textContent = 'Tersalin!';

            window.setTimeout(() => {
                button.textContent = original;
            }, 1500);
        });
    });
}

/* ------------------------------------------------------------------ */
/* Theme toggle (command palette style quick switch)                   */
/* ------------------------------------------------------------------ */

function initStarRatings() {
    document.querySelectorAll('[data-star-input]').forEach((group) => {
        const input = group.querySelector('input[type="hidden"]');
        const stars = group.querySelectorAll('[data-star]');

        const paint = (value) => {
            stars.forEach((star) => {
                const active = Number(star.dataset.star) <= value;
                star.classList.toggle('text-warning', active);
                star.classList.toggle('text-muted', ! active);
            });
        };

        stars.forEach((star) => {
            star.addEventListener('mouseenter', () => paint(Number(star.dataset.star)));
            star.addEventListener('mouseleave', () => paint(Number(input?.value || 0)));
            star.addEventListener('click', () => {
                if (input) {
                    input.value = star.dataset.star;
                }

                paint(Number(star.dataset.star));
            });
        });

        paint(Number(input?.value || 0));
    });
}

function initSearchShortcut() {
    const search = document.querySelector('[data-search-input]');

    if (! search) {
        return;
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === '/' && document.activeElement !== search) {
            event.preventDefault();
            search.focus();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initPasswordVisibility();
    initFlashMessages();
    initSidebar();
    initDropdownMenus();
    initConfirmations();
    initCopyButtons();
    initStarRatings();
    initSearchShortcut();
    initPageTransitions();
});
