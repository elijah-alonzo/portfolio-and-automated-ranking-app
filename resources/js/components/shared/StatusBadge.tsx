import clsx from 'clsx';
import type { EvaluationStatus, FormStatus } from '@/types/councils';

const evaluation: Record<EvaluationStatus, [string, string]> = {
    closed: ['Closed', 'bg-wait-tint text-wait'],
    ongoing: ['Open', 'bg-brand-tint text-brand'],
    completed: ['Completed', 'bg-ok-tint text-ok'],
};

const form: Record<FormStatus, [string, string]> = {
    pending: ['Not started', 'bg-paper text-muted ring-1 ring-inset ring-line'],
    draft: ['Draft', 'bg-wait-tint text-wait'],
    submitted: ['Submitted', 'bg-ok-tint text-ok'],
};

const pill = 'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium whitespace-nowrap';

/** An evaluation's stage: closed while the adviser sets it up, open for forms, then completed. */
export function EvaluationBadge({ status }: { status: EvaluationStatus }) {
    const [text, tone] = evaluation[status];

    return <span className={clsx(pill, tone)}>{text}</span>;
}

/** Where a single form stands. */
export function FormBadge({ status }: { status: FormStatus }) {
    const [text, tone] = form[status];

    return <span className={clsx(pill, tone)}>{text}</span>;
}
