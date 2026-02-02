import type { LucideIcon } from 'lucide-vue-next';

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
