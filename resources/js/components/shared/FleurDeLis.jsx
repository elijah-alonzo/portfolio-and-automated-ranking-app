/**
 * Fleur-de-lis drawn from public/fleur-de-lis.png, used as a mask so it
 * takes the current text colour (e.g. `text-brand-deep`).
 */
export default function FleurDeLis({ className = '' }) {
    return (
        <span
            aria-hidden="true"
            className={`block aspect-[226/246] bg-current ${className}`}
            style={{
                maskImage: 'url(/fleur-de-lis.png)',
                WebkitMaskImage: 'url(/fleur-de-lis.png)',
                maskSize: 'contain',
                WebkitMaskSize: 'contain',
                maskRepeat: 'no-repeat',
                WebkitMaskRepeat: 'no-repeat',
                maskPosition: 'center',
                WebkitMaskPosition: 'center',
            }}
        />
    );
}
