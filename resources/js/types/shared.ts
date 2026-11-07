export type AuthUser = {
    id: number;
    name: string;
    firstName: string;
    email: string;
    /** Photo URL, or null to show the initial. */
    avatar: string | null;
    role: string;
    isAdmin: boolean;
};

export type AppNotification = {
    id: string;
    title: string;
    message: string;
    tone: 'action' | 'success' | 'danger' | 'info';
    read: boolean;
    at: string;
};

export type SharedProps = {
    appName: string;
    appUrl: string;
    auth: { user: AuthUser | null };
    /** What the navigation offers. Null when signed out. */
    can: { manageStudents: boolean; portfolio: boolean } | null;
    /** Null when signed out. */
    notifications: { unread: number; recent: AppNotification[] } | null;
};
