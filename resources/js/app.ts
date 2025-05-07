import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';

// Font awesome
import { library, dom } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { fas } from '@fortawesome/free-solid-svg-icons'
import { fab } from '@fortawesome/free-brands-svg-icons';
import { far } from '@fortawesome/free-regular-svg-icons';
library.add(fas, far, fab);
dom.watch();

// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Widget Types - (When adding to this list, make sure to create the template)
const widgetOptions = [
    { id: 1, name: 'cards_2_across', description: '2 cards in a row', label: 'Cards 2 Across' , path: 'cards/Cards2Across'},
    { id: 2, name: 'cards_3_across', description: '3 cards in a row', label: 'Cards 3 Across' , path: 'cards/Cards3Across' },
    { id: 3, name: 'cards_4_across', description: '4 cards in a row', label: 'Cards 4 Across' , path: 'cards/Cards4Across' },
    { id: 4, name: 'side_by_side', description: 'Show a text paragraph next to a big image', label: 'Side by Side' , path: 'cards/Cards2Across' },
    { id: 5, name: 'side_by_side_full', description: 'Show a text paragraph next to a big image (full screen)', label: 'Side by Side (Full Screen)' , path: 'cards/Cards2Across'},
];

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component("font-awesome-icon", FontAwesomeIcon)
            
        app.config.globalProperties.$widgetOptions = widgetOptions; 
        app.config.globalProperties.$appName = appName;  
        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
