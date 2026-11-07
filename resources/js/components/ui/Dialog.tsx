import { X } from 'lucide-react';
import { useEffect, useRef, type PropsWithChildren } from 'react';

type DialogProps = PropsWithChildren<{
    open: boolean;
    onClose: () => void;
    title: string;
    description?: string;
}>;

/** A modal built on the native <dialog>, so focus trapping and Escape come for free. */
export default function Dialog({ open, onClose, title, description, children }: DialogProps) {
    const ref = useRef<HTMLDialogElement>(null);

    useEffect(() => {
        const dialog = ref.current;
        if (!dialog) return;

        if (open && !dialog.open) dialog.showModal();
        if (!open && dialog.open) dialog.close();
    }, [open]);

    return (
        <dialog
            ref={ref}
            onClose={onClose}
            onClick={(event) => {
                if (event.target === ref.current) onClose();
            }}
            className="m-auto w-[min(28rem,calc(100vw-2rem))] rounded-xl bg-surface p-0 text-ink shadow-xl ring-1 ring-gray-950/5 backdrop:bg-brand-deep/40"
        >
            {open && (
                <div className="p-6">
                    <div className="mb-5 flex items-start justify-between gap-4">
                        <div>
                            <h2 className="text-lg font-semibold">{title}</h2>
                            {description && <p className="mt-1 text-sm text-muted">{description}</p>}
                        </div>
                        <button
                            type="button"
                            onClick={onClose}
                            aria-label="Close"
                            className="rounded-md p-1 text-muted hover:text-ink focus-visible:outline-2 focus-visible:outline-brand"
                        >
                            <X size={18} aria-hidden />
                        </button>
                    </div>
                    {children}
                </div>
            )}
        </dialog>
    );
}
