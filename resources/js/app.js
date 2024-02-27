import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import InvoiceIndex from './Components/invoices/Index.vue';
import InvoiceCreate from './Components/invoices/New.vue';
import InvoiceShow from './Components/invoices/Show.vue';
import NotFound from './Components/NotFound.vue';
import { createRouter, createWebHistory } from 'vue-router';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const routes = [
    { path: '/', component: InvoiceIndex},
    { path: '/invoice/new', component: InvoiceCreate},
    { path: '/invoice/show/:id', component: InvoiceShow, props: true },
    { path: '/:pathMatch(.*)*', component: NotFound}
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(router)
            .provide('router', router)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
