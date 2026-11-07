import '@inertiajs/core';
import type { SharedProps } from '@/types/shared';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        // Props every page receives (HandleInertiaRequests::share), so a bare
        // `usePage()` is typed without a generic.
        sharedPageProps: SharedProps;
        // One-time flash data sent with `Inertia::flash()` on the server. It is
        // not kept in browser history, so going Back never replays a toast.
        flashDataType: {
            success?: string;
            error?: string;
        };
    }
}
