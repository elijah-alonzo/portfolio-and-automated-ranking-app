import clsx from 'clsx';

export type ButtonVariant = 'primary' | 'secondary' | 'danger' | 'quiet';

const variants: Record<ButtonVariant, string> = {
    primary: 'bg-brand text-on-brand shadow-sm hover:bg-brand-strong focus-visible:outline-brand',
    secondary: 'bg-surface text-ink shadow-sm ring-1 ring-gray-950/10 hover:bg-paper focus-visible:outline-brand',
    danger: 'bg-bad text-white shadow-sm hover:opacity-90 focus-visible:outline-bad',
    quiet: 'text-muted hover:text-ink focus-visible:outline-brand',
};

/** Shared by `Button` and by links that should look like one. */
export function buttonClass(variant: ButtonVariant = 'primary'): string {
    return clsx(
        'inline-flex items-center justify-center gap-1.5 rounded-lg px-3.5 py-2 text-sm font-semibold transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 disabled:pointer-events-none disabled:opacity-60',
        variants[variant]
    );
}

/** Inputs, selects and textareas, styled like the admin panel's fields. */
export const control =
    'w-full rounded-lg border-0 bg-surface px-3 py-2 text-sm text-ink shadow-sm ring-1 ring-gray-950/10 transition placeholder:text-muted focus:ring-2 focus:ring-brand focus:outline-none disabled:bg-paper disabled:text-muted aria-invalid:ring-bad';

export const label = 'mb-1.5 block text-sm font-medium text-ink';

/** The card surface, matching the admin panel's sections. */
export const card = 'rounded-xl bg-surface shadow-sm ring-1 ring-gray-950/5';
