// utils/sharedData.ts
import { User } from '@/pages/User/data/schema';
import { usePage } from '@inertiajs/vue3';

interface Notification {
    id: string;
    title: string;
    message: string;
    created_at: string;
}
let cachedCsrftoken: string | null = null;
let locale: string | null = null;
let cachedAdmin: User | null = null;
let cachedNotification: Notification[] | null = null;

export function getNotification(): Notification[] {
    try {
        const page = usePage();
        cachedNotification = page.props.notifications as Notification[];
        return cachedNotification;
    } catch (error) {
        console.error('Failed to get admin data:', error);
        return {} as Notification[];
    }
}
export function getRole() {
    try {
        const page = usePage();
        return page.props.roles;
    } catch (error) {
        console.error('Failed to get admin data:', error);
        return null;
    }
}
export function getAdmin() {
    try {
        const page = usePage();
        cachedAdmin = page.props?.admin as User | null;
        return cachedAdmin;
    } catch (error) {
        console.error('Failed to get admin data:', error);
        return null;
    }
}

export async function getCsrfToken(): Promise<string> {
    // Try to get CSRF token from meta tag first
    const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (metaToken) {
        return metaToken;
    }

    // Fallback: fetch from Laravel
    try {
        const response = await fetch('/sanctum/csrf-cookie', {
            credentials: 'same-origin',
        });

        if (response.ok) {
            // Get token from cookie or meta tag after the request
            const updatedMetaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (updatedMetaToken) {
                return updatedMetaToken;
            }
        }
    } catch (error) {
        console.error('Failed to fetch CSRF token:', error);
    }

    throw new Error('Unable to retrieve CSRF token');
}

export function getLocale(): string {
    try {
        const page = usePage();
        locale = page.props.locale as string;
        return locale;
    } catch (error) {
        console.error('Failed to get locale:', error);
        return '';
    }
}
