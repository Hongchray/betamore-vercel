import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import PhosphorIcons from '@phosphor-icons/vue';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { i18nVue } from 'laravel-vue-i18n';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';

// Define shared props type
interface PageProps {
    siteInfo?: {
        site_name?: string;
        logo_url?: string;
        favicon_url?: string;
        meta_description?: string;
        prefix?: string[];
    };
    title?: string;
    [key: string]: any; // For other props like auth, flash, etc.
}

// Temporary fallback
let appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const siteName = props.initialPage.props.siteInfo?.site_name;
        if (siteName) {
            appName = siteName;
            const pageTitle = props.initialPage.props.title;
            document.title = pageTitle ? `${pageTitle} - ${appName}` : appName;
        }

        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18nVue, {
                resolve: async (lang: string) => {
                    const langs = import.meta.glob('../../lang/*.json');
                    return await langs[`../../lang/${lang}.json`]();
                },
            });

        app.config.globalProperties.$updateFont = (locale: string) => {
            const fontMap: Record<string, string> = {
                en: 'Inter, sans-serif',
                km: 'Kantumruy Pro, Khmer OS, sans-serif',
                zh: 'Noto Sans SC, PingFang SC, sans-serif',
                ar: 'Noto Sans Arabic, Tahoma, sans-serif',
            };

            const fontFamily = fontMap[locale] || fontMap['en'];
            document.documentElement.style.setProperty('--font-family', fontFamily);
        };

        app.use(VueApexCharts as any);
        app.use(PhosphorIcons);
        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

initializeTheme();
