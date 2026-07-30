import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';

createInertiaApp({
    // Sem "eager: true": cada página vira o seu próprio ficheiro (code-splitting) em vez de
    // tudo ficar despejado num único bundle gigante — o "prefetch" já usado nos links do menu
    // (AppLayout.vue) carrega o chunk da próxima página em segundo plano antes do clique.
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        return pages[`./Pages/${name}.vue`]();
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#c9a24b',
    },
});

// Mantém o tema em <html data-theme="..."> sincronizado após navegações Inertia (SPA).
router.on('finish', () => {
    document.documentElement.setAttribute('data-theme', localStorage.getItem('alvoflow-theme') || 'light');
});

// Transição fluida entre páginas: fade sutil durante a navegação Inertia.
router.on('start', () => document.getElementById('app')?.classList.add('is-navigating'));
router.on('finish', () => document.getElementById('app')?.classList.remove('is-navigating'));

// Sessão expirada (419) ou erro de servidor: em vez do popup padrão do Inertia com a
// página de erro do Laravel dentro de um iframe, manda para o login de forma limpa.
router.on('invalid', (event) => {
    const status = event.detail.response?.status;
    if (status === 419 || status === 401) {
        event.preventDefault();
        window.location.href = '/login';
    }
});

// PWA: regista o service worker (torna a app instalável e cacheia só os assets estáticos do
// build — ver public/sw.js). "load" evita atrasar o primeiro render à espera do registo.
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}
