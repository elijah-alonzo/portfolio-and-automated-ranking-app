import type { ReactNode } from 'react';
import { label as labelClass } from '@/components/ui/styles';

/** A label, the control it names, and the server's error for it. */
export default function Field({
    label,
    error,
    hint,
    children,
}: {
    label: string;
    error?: string;
    hint?: ReactNode;
    children: ReactNode;
}) {
    return (
        <label className="block">
            <span className={labelClass}>{label}</span>
            {children}
            {hint && !error && <span className="mt-1.5 block text-xs text-muted">{hint}</span>}
            {error && <span className="mt-1.5 block text-sm text-bad">{error}</span>}
        </label>
    );
}
