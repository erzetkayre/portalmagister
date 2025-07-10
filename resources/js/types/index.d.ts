import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    routeName?: string;
    icon?: LucideIcon;
    isActive?: boolean;
    items?: NavItem[];
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    nama: string;
    email: string;
    nomor_induk: string;
    photo?: string;
    phone?: string;
    email_verified_at: string | null;
    first_login: boolean;
    is_active: boolean;
    role_id: number;
    role_data?: {
        gender?: string;
        nama_mhs?: string;
        nama_dosen?: string;
    };
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
