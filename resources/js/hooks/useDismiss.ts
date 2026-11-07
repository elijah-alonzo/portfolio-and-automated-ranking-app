import { useEffect, type RefObject } from 'react';

/**
 * Closes a popover on a click outside `root` or on Escape. Escape also puts
 * focus back on `trigger`, so keyboard users land where they started.
 */
export function useDismiss(
    isOpen: boolean,
    close: () => void,
    root: RefObject<HTMLElement | null>,
    trigger: RefObject<HTMLElement | null>
) {
    useEffect(() => {
        if (!isOpen) return;

        function onPointer(event: PointerEvent) {
            if (!root.current?.contains(event.target as Node)) close();
        }

        function onKey(event: KeyboardEvent) {
            if (event.key !== 'Escape') return;
            close();
            trigger.current?.focus();
        }

        document.addEventListener('pointerdown', onPointer);
        document.addEventListener('keydown', onKey);

        return () => {
            document.removeEventListener('pointerdown', onPointer);
            document.removeEventListener('keydown', onKey);
        };
    }, [isOpen, close, root, trigger]);
}
