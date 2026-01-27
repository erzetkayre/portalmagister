import { AppPageProps } from '@/types/index';

// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

declare module '@inertiajs/core' {
  interface PageProps extends InertiaPageProps {
    auth: {
      user: {
        id: number
        name: string
        study_program: string
      } | null
      can: {
        admin: boolean
        koordinator: boolean
        dosen: boolean
        mahasiswa: boolean
      }
      program: 'pwk' | 'elektro'
    }
  }
}

declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        $inertia: typeof Router;
        $page: Page;
        $headManager: ReturnType<typeof createHeadManager>;
    }
}
