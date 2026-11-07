import ShowcasePanel from '@/components/sections/login/ShowcasePanel';
import SignInForm from '@/components/sections/login/SignInForm';
import Seo from '@/components/shared/Seo';

type LoginProps = {
    stats: {
        activeCouncils: number;
        studentOfficers: number;
        ongoingEvaluations: number;
    };
};

export default function Login({ stats }: LoginProps) {
    return (
        <div className="grid min-h-dvh place-items-center bg-[radial-gradient(ellipse_at_top,var(--brand-tint),var(--surface)_70%)] p-4 sm:p-8">
            <Seo
                title="Sign in"
                description="Sign in to the Paulinian Student Government e-Portfolio and Ranking System."
                index
            />

            <main className="grid w-full max-w-[1200px] overflow-hidden rounded-xl bg-surface shadow-sm ring-1 ring-gray-950/5 lg:min-h-[min(760px,calc(100dvh-4rem))] lg:grid-cols-[1fr_1.05fr]">
                <section className="flex flex-col px-6 py-8 sm:px-14 sm:py-12">
                    <img
                        src="/logo-02.png"
                        alt="Paulinian Student Government e-Portfolio and Ranking System"
                        className="h-11 w-auto self-center object-contain"
                    />

                    <div className="flex flex-1 items-center justify-center py-12">
                        <SignInForm />
                    </div>

                    <footer className="space-y-1 text-center text-xs text-muted">
                        <p>
                            {stats.activeCouncils} active councils, {stats.studentOfficers} student officers,{' '}
                            {stats.ongoingEvaluations} evaluations in progress.
                        </p>
                        <p>
                            © {new Date().getFullYear()} Paulinian Student Government, St. Paul University Philippines.
                        </p>
                    </footer>
                </section>

                <ShowcasePanel />
            </main>
        </div>
    );
}
