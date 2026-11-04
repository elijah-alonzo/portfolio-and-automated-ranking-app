import { useEffect, useRef } from 'react';

export default function useReveal() {
    const ref = useRef(null);

    useEffect(() => {
        const node = ref.current;

        if (!node || !('IntersectionObserver' in window)) {
            node?.classList.add('is-visible');

            return undefined;
        }

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    node.classList.add('is-visible');
                    observer.disconnect();
                }
            },
            { threshold: 0.15 },
        );

        observer.observe(node);

        return () => observer.disconnect();
    }, []);

    return ref;
}
