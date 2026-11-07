import { Link, router, usePage, usePoll } from '@inertiajs/react';
import clsx from 'clsx';
import { Bell } from 'lucide-react';
import { useId, useRef, useState } from 'react';
import { open as openNotification, readAll } from '@/actions/App/Features/Users/Controllers/NotificationController';
import { useDismiss } from '@/hooks/useDismiss';
import { timeAgo } from '@/lib/format';
import type { AppNotification } from '@/types/shared';

const toneDot: Record<AppNotification['tone'], string> = {
    action: 'bg-brand',
    success: 'bg-ok',
    danger: 'bg-bad',
    info: 'bg-muted',
};

export default function NotificationBell() {
    const { notifications } = usePage().props;
    const [isOpen, setIsOpen] = useState(false);
    const root = useRef<HTMLDivElement>(null);
    const button = useRef<HTMLButtonElement>(null);
    const panelId = useId();

    // New notifications arrive without a page change.
    usePoll(30_000, { only: ['notifications'] });

    useDismiss(isOpen, () => setIsOpen(false), root, button);

    if (!notifications) return null;

    const { unread, recent } = notifications;

    return (
        <div ref={root} className="relative">
            <button
                ref={button}
                type="button"
                onClick={() => setIsOpen((value) => !value)}
                aria-expanded={isOpen}
                aria-controls={panelId}
                aria-label={unread > 0 ? `Notifications, ${unread} unread` : 'Notifications'}
                className="relative grid size-9 place-items-center rounded-full text-muted hover:bg-paper transition-colors hover:text-ink focus-visible:outline-2 focus-visible:outline-brand"
            >
                <Bell size={16} aria-hidden />
                {unread > 0 && (
                    <span
                        aria-hidden
                        className="absolute -top-1.5 -right-1.5 grid h-4.5 min-w-4.5 place-items-center rounded-full bg-brand px-1 text-[0.6875rem] font-semibold text-on-brand tabular-nums"
                    >
                        {unread > 9 ? '9+' : unread}
                    </span>
                )}
            </button>

            {isOpen && (
                <div
                    id={panelId}
                    className="absolute right-0 z-30 mt-2 w-[min(22rem,calc(100vw-2rem))] overflow-hidden rounded-xl bg-surface shadow-lg ring-1 ring-gray-950/5"
                >
                    <div className="flex items-center justify-between border-b border-line px-4 py-3">
                        <h2 className="text-sm font-semibold">Notifications</h2>
                        {unread > 0 && (
                            <button
                                type="button"
                                onClick={() => router.post(readAll.url(), {}, { preserveScroll: true })}
                                className="text-xs font-medium text-brand hover:underline focus-visible:outline-2 focus-visible:outline-brand"
                            >
                                Mark all as read
                            </button>
                        )}
                    </div>

                    {recent.length === 0 ? (
                        <p className="px-4 py-8 text-center text-sm text-muted">
                            Nothing yet. You will hear here when an evaluation opens or a form needs you.
                        </p>
                    ) : (
                        <ul className="max-h-[70vh] divide-y divide-line overflow-y-auto">
                            {recent.map((item) => (
                                <li key={item.id}>
                                    <Link
                                        href={openNotification.url(item.id)}
                                        method="post"
                                        as="button"
                                        onClick={() => setIsOpen(false)}
                                        className={clsx(
                                            'flex w-full gap-3 px-4 py-3 text-left transition-colors hover:bg-paper focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-brand',
                                            !item.read && 'bg-brand-tint/50'
                                        )}
                                    >
                                        <span
                                            aria-hidden
                                            className={clsx(
                                                'mt-1.5 size-2 shrink-0 rounded-full',
                                                item.read ? 'bg-line' : toneDot[item.tone]
                                            )}
                                        />
                                        <span className="min-w-0 text-sm">
                                            <span className="block truncate font-medium">{item.title}</span>
                                            <span className="block text-muted">{item.message}</span>
                                            <span className="mt-1 block text-xs text-muted">
                                                {timeAgo(item.at)}
                                                {!item.read && <span className="sr-only">, unread</span>}
                                            </span>
                                        </span>
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    )}
                </div>
            )}
        </div>
    );
}
