import { showcase } from '@/data/login';

export default function ShowcasePanel() {
    return (
        <aside className="relative isolate hidden overflow-hidden bg-brand-deep lg:block">
            <img
                src={showcase.image}
                alt={showcase.imageAlt}
                className="absolute inset-0 -z-20 size-full object-cover object-[48%_center]"
            />
            <div
                aria-hidden="true"
                className="absolute inset-0 -z-10 bg-gradient-to-t from-brand-deep via-brand/55 to-brand-deep/10 mix-blend-multiply"
            />
            <div
                aria-hidden="true"
                className="absolute inset-x-0 bottom-0 -z-10 h-2/3 bg-gradient-to-t from-brand-deep/90 to-transparent"
            />

            <div className="flex h-full flex-col justify-end p-12 text-white">
                <span aria-hidden="true" className="mb-6 block h-1 w-12 rounded-full bg-gold" />
                <p className="max-w-[14ch] font-display font-medium text-[clamp(2.6rem,2.4vw+1.4rem,3.75rem)] leading-[1.05]">
                    {showcase.headline}
                </p>
                <div className="mt-6 flex items-end justify-between gap-8">
                    <p className="max-w-[38ch] text-[15px] leading-relaxed text-white/80">{showcase.body}</p>
                    <img src="/logo-01.png" alt="" className="size-16 shrink-0 drop-shadow-lg" />
                </div>
            </div>
        </aside>
    );
}
