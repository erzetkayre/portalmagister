import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

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
    auth: AuthProps;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
};

export interface AuthUser {
    id: number;
    name: string;
    email: string;
    nomor_induk: string;
    study_program: string;
    photo?: string;
    phone?: string;
}

export interface AuthCan {
    admin: boolean;
    koordinator: boolean;
    dosen: boolean;
    mahasiswa: boolean;
}

export interface AuthProps {
    user: AuthUser | null
    can: AuthCan
    program: string | null
}

export type BreadcrumbItemType = BreadcrumbItem;
