import type { ReactNode } from 'react';

/** The top of every app page: a serif title, one line of context, and the page's main actions. */
export default function PageHeader({
    title,
    description,
    actions,
    eyebrow,
}: {
    title: string;
    description?: ReactNode;
    actions?: ReactNode;
    /** A link back up, shown above the title. */
    eyebrow?: ReactNode;
}) {
    return (
        <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div className="min-w-0">
                {eyebrow && <div className="mb-2 text-sm">{eyebrow}</div>}
                <h1 className="font-display font-medium text-[2rem] leading-tight text-brand-deep sm:text-[2.25rem]">
                    {title}
                </h1>
                {description && <p className="mt-1.5 max-w-2xl text-sm text-muted">{description}</p>}
            </div>
            {actions && <div className="flex flex-wrap items-center gap-2">{actions}</div>}
        </div>
    );
}
