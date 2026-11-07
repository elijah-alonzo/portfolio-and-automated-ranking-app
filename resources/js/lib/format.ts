const date = new Intl.DateTimeFormat('en', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
});

const relative = new Intl.RelativeTimeFormat('en', { numeric: 'auto' });

export function formatDate(iso: string | null): string {
    return iso ? date.format(new Date(iso)) : '';
}

/** A final score as the rubric writes it: two decimals, out of 3.00. */
export function formatScore(score: number): string {
    return score.toFixed(2);
}

export function formatMegabytes(kb: number): string {
    return `${Math.floor((kb / 1024) * 10) / 10} MB`;
}

export function timeAgo(iso: string | null): string {
    if (!iso) return '';

    const seconds = (new Date(iso).getTime() - Date.now()) / 1000;
    const steps: [Intl.RelativeTimeFormatUnit, number][] = [
        ['day', 86400],
        ['hour', 3600],
        ['minute', 60],
    ];

    for (const [unit, size] of steps) {
        if (Math.abs(seconds) >= size) {
            return relative.format(Math.round(seconds / size), unit);
        }
    }

    return 'just now';
}
