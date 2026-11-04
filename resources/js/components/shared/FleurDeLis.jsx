export default function FleurDeLis({ className, tailFill = 'currentColor', ...props }) {
    return (
        <svg viewBox="0 0 200 212" fill="currentColor" className={className} aria-hidden="true" {...props}>
            {/* Center petal */}
            <path d="M100 0C110 18 130 38 130 66 130 90 116 102 110 120H90C84 102 70 90 70 66 70 38 90 18 100 0Z" />
            {/* Side petals */}
            <path
                id="fleur-petal"
                d="M88 126C78 96 40 72 14 86 0 94-4 116 2 132 6 138 12 140 12 140 20 124 36 118 54 120 68 122 80 130 88 140Z"
            />
            <use href="#fleur-petal" transform="matrix(-1 0 0 1 200 0)" />
            {/* Band */}
            <rect x="62" y="128" width="76" height="16" rx="8" />
            <g fill={tailFill}>
                {/* Lower curls */}
                <path
                    id="fleur-curl"
                    d="M84 150C70 148 52 152 38 164 30 170 28 178 30 184 36 174 52 168 70 172 78 174 84 178 88 184Z"
                />
                <use href="#fleur-curl" transform="matrix(-1 0 0 1 200 0)" />
                {/* Bottom spike */}
                <path d="M100 212C106 196 112 184 110 170 109 162 108 156 108 150H92C92 156 91 162 90 170 88 184 94 196 100 212Z" />
            </g>
        </svg>
    );
}
