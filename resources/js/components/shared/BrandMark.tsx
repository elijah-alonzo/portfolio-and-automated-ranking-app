/**
 * The app logo: the icon (public/logo-01.png), or with `full` the icon with the name (public/logo-02.png).
 * Decorative: the app name always sits beside it, visibly or as screen-reader text.
 */
export default function BrandMark({
    full = false,
    className = full ? 'h-8 w-auto' : 'size-7',
}: {
    full?: boolean;
    className?: string;
}) {
    return <img src={full ? '/logo-02.png' : '/logo-01.png'} alt="" className={className} />;
}
