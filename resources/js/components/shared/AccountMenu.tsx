import { Link, router, usePage } from '@inertiajs/react';
import { LogOut, Settings, UserRound } from 'lucide-react';
import { useId, useRef, useState } from 'react';
import Avatar from '@/components/shared/Avatar';
import { useDismiss } from '@/hooks/useDismiss';
import { edit as profile } from '@/actions/App/Features/Users/Controllers/ProfileController';
import { logout } from '@/routes';

const row =
    'flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm transition-colors hover:bg-paper focus-visible:bg-paper focus-visible:outline-none';

/** The settings button in the header: who you are, your profile and sign out. */
export default function AccountMenu() {
    const { auth } = usePage().props;
    const [isOpen, setIsOpen] = useState(false);
    const root = useRef<HTMLDivElement>(null);
    const button = useRef<HTMLButtonElement>(null);
    const panelId = useId();

    useDismiss(isOpen, () => setIsOpen(false), root, button);

    const user = auth.user;
    if (!user) return null;

    return (
        <div ref={root} className="relative">
            <button
                ref={button}
                type="button"
                onClick={() => setIsOpen((value) => !value)}
                aria-expanded={isOpen}
                aria-controls={panelId}
                aria-label="Account"
                className="flex items-center gap-2 rounded-full p-0.5 pr-2 transition-colors hover:bg-paper focus-visible:outline-2 focus-visible:outline-brand"
            >
                <Avatar name={user.name} src={user.avatar} className="size-8 text-sm" />
                <Settings size={15} aria-hidden className="text-muted" />
            </button>

            {isOpen && (
                <div
                    id={panelId}
                    className="absolute right-0 z-30 mt-2 w-[min(17rem,calc(100vw-2rem))] overflow-hidden rounded-xl bg-surface shadow-lg ring-1 ring-gray-950/5"
                >
                    <div className="flex items-center gap-3 border-b border-line px-4 py-3.5">
                        <Avatar name={user.name} src={user.avatar} />
                        <div className="min-w-0 leading-tight">
                            <p className="truncate text-sm font-medium">{user.name}</p>
                            <p className="truncate text-xs text-muted">{user.role}</p>
                        </div>
                    </div>

                    <div className="py-1.5">
                        <Link href={profile.url()} onClick={() => setIsOpen(false)} className={row}>
                            <UserRound size={16} aria-hidden className="text-muted" />
                            Profile
                        </Link>
                    </div>

                    <div className="border-t border-line py-1.5">
                        <button
                            type="button"
                            onClick={() => router.post(logout.url())}
                            className={`${row} hover:text-bad`}
                        >
                            <LogOut size={16} aria-hidden />
                            Sign out
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}
