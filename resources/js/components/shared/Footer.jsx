const linkClass = 'text-white/85 hover:text-white hover:underline';

export default function Footer() {
    return (
        <footer
            id="contact"
            className="relative bg-brand-dark bg-[url('/sys-footer.png')] bg-cover bg-center py-9 text-white before:absolute before:inset-0 before:bg-brand-dark/90 before:content-['']"
        >
            <div className="container-page relative z-10 grid grid-cols-[repeat(auto-fit,minmax(220px,1fr))] gap-5 text-sm">
                <div>
                    <div className="mb-2.5 flex items-center gap-3">
                        <img className="size-11 object-contain" src="/sys-brand.png" alt="Paulinian Student Government seal" />
                        <div>
                            <div className="mb-2.5 font-semibold">Paulinian Student Government</div>
                            <div>E-Portfolio and Ranking System</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div className="mb-2.5 font-semibold">Student Affairs and Academic Services</div>
                    <ul className="grid gap-1.5">
                        <li>St. Paul University Philippines</li>
                        <li>
                            <a className={linkClass} href="mailto:psg@school.edu">
                                psg@school.edu
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <div className="mb-2.5 font-semibold">Connect with PSG</div>
                    <ul className="grid gap-1.5">
                        <li>
                            <a
                                className={linkClass}
                                href="https://www.facebook.com/PaulinianStudentGovernment"
                                target="_blank"
                                rel="noreferrer"
                            >
                                Facebook: Paulinian Student Government
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </footer>
    );
}
