import type { ReactNode } from 'react';

/** What to show when a list has nothing in it: what it will hold, and what to do next. */
export default function EmptyState({ title, children }: { title: string; children?: ReactNode }) {
    return (
        <div className="px-6 py-12 text-center">
            <p className="font-medium">{title}</p>
            {children && <div className="mx-auto mt-1 max-w-sm text-sm text-muted">{children}</div>}
        </div>
    );
}
