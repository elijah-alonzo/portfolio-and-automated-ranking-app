const links = [
    { href: '#features', label: 'Features' },
    { href: '#ranking', label: 'Ranking' },
    { href: '#councils', label: 'Councils' },
];

export default function Navigation() {
    return (
        <header className="sticky top-0 z-30 border-b border-line bg-paper/80 backdrop-blur">
            <nav className="mx-auto flex max-w-6xl items-center justify-between px-6 py-3">
                <a href="/" className="flex items-center gap-3">
                    <img src="/sys-logo.png" alt="" className="h-9 w-9" />
                    <span className="text-sm font-semibold tracking-tight">
                        PSG E-Portfolio
                    </span>
                </a>
                <ul className="hidden items-center gap-8 text-sm text-muted md:flex">
                    {links.map((link) => (
                        <li key={link.href}>
                            <a
                                href={link.href}
                                className="transition hover:text-ink"
                            >
                                {link.label}
                            </a>
                        </li>
                    ))}
                </ul>
                <a href="/ranking/login" className="btn btn-primary">
                    Sign in
                </a>
            </nav>
        </header>
    );
}
