import { createInertiaApp } from '@inertiajs/react';
import type { ComponentType } from 'react';
import { createRoot } from 'react-dom/client';

const pages = import.meta.glob<{ default: ComponentType }>('./pages/**/*.tsx', {
    eager: true,
});

void createInertiaApp({
    // `Seo` writes the full title, app name included.
    title: (title) => title,
    resolve: (name) => pages[`./pages/${name}.tsx`],
    // The loading bar follows the brand token.
    progress: { color: 'var(--brand)' },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
