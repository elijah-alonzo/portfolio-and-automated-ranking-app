import { Link, usePage } from '@inertiajs/react';
import clsx from 'clsx';
import type { PropsWithChildren } from 'react';
import AccountMenu from '@/components/shared/AccountMenu';
import BrandMark from '@/components/shared/BrandMark';
import NotificationBell from '@/components/shared/NotificationBell';
import Toasts from '@/components/shared/Toasts';
import HomeController from '@/actions/App/Features/Home/Controllers/HomeController';
import { index as councils } from '@/actions/App/Features/Evaluations/Controllers/CouncilController';
import { mine as portfolio } from '@/actions/App/Features/Users/Controllers/PortfolioController';
import { index as students } from '@/actions/App/Features/Users/Controllers/StudentController';
import { index as certificates } from '@/actions/App/Features/Certificates/Controllers/CertificateController';

export default function AppLayout({ children }: PropsWithChildren) {
    const { appName, can, url } = { ...usePage().props, url: usePage().url };

    const links = [
        { href: HomeController.url(), label: 'Home', exact: true },
        { href: councils.url(), label: 'My Councils' },
        can?.portfolio && { href: portfolio.url(), label: 'Portfolio' },
        can?.manageStudents && { href: students.url(), label: 'Students' },
        { href: certificates.url(), label: 'Certificates' },
    ].filter((link) => link !== false && link !== undefined) as {
        href: string;
        label: string;
        exact?: boolean;
    }[];

    const path = url.split('?')[0];
    const isActive = (href: string, exact?: boolean) =>
        exact ? path === href : path === href || path.startsWith(`${href}/`);

    return (
        <div className="min-h-screen bg-paper text-ink">
            <a
                href="#main"
                className="sr-only rounded-md bg-surface px-3 py-2 text-sm font-medium focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-30 focus:outline-2 focus:outline-brand"
            >
                Skip to content
            </a>
            <header className="sticky top-0 z-20 border-b border-line bg-surface/95 backdrop-blur">
                <div className="mx-auto flex h-16 max-w-6xl items-center gap-6 px-4">
                    <Link
                        href={HomeController.url()}
                        className="shrink-0 focus-visible:outline-2 focus-visible:outline-brand"
                    >
                        <BrandMark full className="h-9 w-auto" />
                        <span className="sr-only">{appName}</span>
                    </Link>
                    <nav aria-label="Main" className="hidden flex-1 items-center gap-1 md:flex">
                        {links.map((link) => (
                            <NavLink key={link.href} href={link.href} active={isActive(link.href, link.exact)}>
                                {link.label}
                            </NavLink>
                        ))}
                    </nav>
                    <div className="ml-auto flex items-center gap-2">
                        <NotificationBell />
                        <AccountMenu />
                    </div>
                </div>
                <nav aria-label="Main" className="flex gap-1 overflow-x-auto border-t border-line px-3 py-2 md:hidden">
                    {links.map((link) => (
                        <NavLink key={link.href} href={link.href} active={isActive(link.href, link.exact)}>
                            {link.label}
                        </NavLink>
                    ))}
                </nav>
            </header>
            <main id="main" className="mx-auto max-w-6xl px-4 py-8 sm:py-10">
                {children}
            </main>
            <Toasts />
        </div>
    );
}

function NavLink({ href, active, children }: PropsWithChildren<{ href: string; active: boolean }>) {
    return (
        <Link
            href={href}
            aria-current={active ? 'page' : undefined}
            className={clsx(
                'rounded-lg px-3 py-1.5 text-sm font-medium whitespace-nowrap transition-colors focus-visible:outline-2 focus-visible:outline-brand',
                active ? 'bg-brand-tint text-brand' : 'text-muted hover:bg-paper hover:text-ink'
            )}
        >
            {children}
        </Link>
    );
}
