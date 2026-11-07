import clsx from 'clsx';

/** A person's photo, or their initial on the brand tint when there is none. Decorative: the name sits beside it. */
export default function Avatar({
    name,
    src,
    className = 'size-9 text-sm',
}: {
    name: string;
    src: string | null;
    className?: string;
}) {
    if (src) {
        return <img src={src} alt="" className={clsx('shrink-0 rounded-full object-cover', className)} />;
    }

    return (
        <span
            aria-hidden
            className={clsx(
                'grid shrink-0 place-items-center rounded-full bg-brand-tint font-semibold text-brand',
                className
            )}
        >
            {name.charAt(0).toUpperCase()}
        </span>
    );
}
