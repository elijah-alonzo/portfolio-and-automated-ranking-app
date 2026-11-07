import clsx from 'clsx';
import type { PropsWithChildren, ReactNode } from 'react';
import { card } from '@/components/ui/styles';

type PanelProps = PropsWithChildren<{
    title?: string;
    description?: ReactNode;
    /** Shown at the right of the heading row. */
    actions?: ReactNode;
    className?: string;
    /** Remove the body padding, for tables and lists that run edge to edge. */
    flush?: boolean;
}>;

/** A card section with an optional heading row, like the admin panel's sections. */
export default function Panel({ title, description, actions, className, flush = false, children }: PanelProps) {
    const hasHeader = title || description || actions;

    return (
        <section className={clsx(card, className)}>
            {hasHeader && (
                <header className="flex flex-wrap items-start justify-between gap-3 border-b border-line px-5 py-4">
                    <div className="min-w-0">
                        {title && <h2 className="text-base font-semibold">{title}</h2>}
                        {description && <p className="mt-0.5 text-sm text-muted">{description}</p>}
                    </div>
                    {actions && <div className="flex flex-wrap items-center gap-2">{actions}</div>}
                </header>
            )}
            <div className={flush ? undefined : 'p-5'}>{children}</div>
        </section>
    );
}
