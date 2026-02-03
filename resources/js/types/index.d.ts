import type { Config } from 'ziggy-js';
export * from './navigation';
export * from './auth';
export * from './user';

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    auth: AuthProps;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
};
