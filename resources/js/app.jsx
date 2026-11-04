
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

createInertiaApp({
    title: (title) => (title ? `${title} | Paulinian Student Government` : 'Paulinian Student Government'),
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.jsx', { eager: true });

        return pages[`./pages/${name}.jsx`];
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
