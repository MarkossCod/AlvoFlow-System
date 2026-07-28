import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
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
