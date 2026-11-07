import { router } from '@inertiajs/react';
import { useEffect } from 'react';
import { Toaster, toast } from 'sonner';

/** The toast area. Shows each server flash exactly once. */
export default function Toasts() {
    useEffect(
        () =>
            router.on('flash', (event) => {
                const { success, error } = event.detail.flash;
                if (success) toast.success(success);
                if (error) toast.error(error);
            }),
        []
    );

    return <Toaster theme="light" position="bottom-right" richColors />;
}
