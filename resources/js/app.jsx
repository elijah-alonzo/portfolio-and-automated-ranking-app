import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

createInertiaApp({
    title: (title) => (title ? `${title} | PSG E-Portfolio` : 'PSG E-Portfolio'),
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.jsx', { eager: true });

        return pages[`./pages/${name}.jsx`];
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
