export default function Header() {
    return (
        <header className="sticky top-0 z-10 border-b-[3px] border-gold bg-white">
            <div className="container-page flex min-h-16 items-center justify-between gap-6 max-sm:flex-wrap">
                <div className="flex items-center gap-3 font-semibold">
                    <img className="h-10 w-auto object-contain" src="/sys-logo.png" alt="Paulinian Student Government logo" />
                </div>
                <nav className="hidden items-center gap-6 text-[15px] min-[961px]:flex">
                    {[
                        ['#about', 'About'],
                        ['#features', 'Features'],
                        ['#contact', 'Contact'],
                    ].map(([href, label]) => (
                        <a
                            key={href}
                            href={href}
                            className="border-b-2 border-transparent py-1 font-medium text-muted transition-colors hover:border-brand hover:text-brand-dark"
                        >
                            {label}
                        </a>
                    ))}
                </nav>
                <div className="flex items-center gap-3">
                    <a className="btn border-ink/15 bg-white text-ink" href="#about">
                        Learn more
                    </a>
                    <a className="btn bg-brand-dark text-white" href="/ranking/login">
                        Login
                    </a>
                </div>
            </div>
        </header>
    );
}
