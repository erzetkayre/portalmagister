<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Map, BookOpen, Award } from 'lucide-vue-next';
</script>

<template>
    <Head title="Magister PWK">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]">
        <header class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
            <nav class="flex items-center justify-between">
                <div class="font-medium text-lg">
                    Magister PWK
                </div>
                <div class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route($page.props.auth.user.role.nama_role + '.dashboard')"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                        >
                            Login
                        </Link>
                    </template>
                </div>
            </nav>
        </header>
        <div class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
            <main class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row">
                <div
                    class="flex-1 rounded-br-lg rounded-bl-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:rounded-tl-lg lg:rounded-br-none lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <h1 class="mb-1 font-medium text-2xl lg:text-3xl">Program Magister Perencanaan Wilayah dan Kota</h1>
                    <p class="mb-4 text-[#706f6c] text-base lg:text-lg dark:text-[#A1A09A]">
                        Membangun keahlian dalam perencanaan pembangunan berkelanjutan, <br />
                        tata ruang, dan pengelolaan wilayah perkotaan yang inovatif.
                    </p>
                    <ul class="mb-4 flex flex-col lg:mb-6">
                        <li
                            class="relative flex items-center gap-4 py-2 before:absolute before:top-1/2 before:bottom-0 before:left-[0.4rem] before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A]"
                        >
                            <span class="relative bg-white py-1 dark:bg-[#161615]">
                                <span
                                    class="flex h-3.5 w-3.5 items-center justify-center rounded-full border border-[#e3e3e0] bg-[#FDFDFC] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615]"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A]" />
                                </span>
                            </span>
                            <span>
                                Kurikulum berbasis
                                <span class="ml-1 font-medium text-[#f53003] dark:text-[#FF4433]">
                                    Tata Ruang Modern
                                </span>
                            </span>
                        </li>
                        <li
                            class="relative flex items-center gap-4 py-2 before:absolute before:top-0 before:bottom-0 before:left-[0.4rem] before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A]"
                        >
                            <span class="relative bg-white py-1 dark:bg-[#161615]">
                                <span
                                    class="flex h-3.5 w-3.5 items-center justify-center rounded-full border border-[#e3e3e0] bg-[#FDFDFC] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615]"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A]" />
                                </span>
                            </span>
                            <span>
                                Pembelajaran kolaboratif dengan
                                <span class="ml-1 font-medium text-[#f53003] dark:text-[#FF4433]">
                                    Praktisi Ahli
                                </span>
                            </span>
                        </li>
                        <li
                            class="relative flex items-center gap-4 py-2 before:absolute before:top-0 before:bottom-1/2 before:left-[0.4rem] before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A]"
                        >
                            <span class="relative bg-white py-1 dark:bg-[#161615]">
                                <span
                                    class="flex h-3.5 w-3.5 items-center justify-center rounded-full border border-[#e3e3e0] bg-[#FDFDFC] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615]"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A]" />
                                </span>
                            </span>
                            <span>
                                Penelitian berkualitas tinggi untuk
                                <span class="ml-1 font-medium text-[#f53003] dark:text-[#FF4433]">
                                    Publikasi Internasional
                                </span>
                            </span>
                        </li>
                    </ul>
                    <ul class="flex gap-3 text-sm leading-normal">
                        <li>
                            <a href="https://spmb.uns.ac.id/jalur-masuk/jenjang?jenjang=s2-s3" target="_blank"
                                class="inline-block rounded-sm border border-black bg-[#1b1b18] px-5 py-1.5 text-sm leading-normal text-white hover:border-black hover:bg-black dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:border-white dark:hover:bg-white"
                            >
                                Daftar Sekarang
                            </a>
                        </li>
                        <li>
                            <a href="https://mpwk.ft.uns.ac.id/" target="_blank"
                                class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                            >
                                Info Lengkap
                            </a>
                        </li>
                    </ul>
                </div>
                <div
                    class="relative -mb-px aspect-335/376 w-full shrink-0 overflow-hidden rounded-t-lg bg-[#fff2f2] lg:mb-0 lg:-ml-px lg:aspect-auto lg:w-[438px] lg:rounded-t-none lg:rounded-r-lg dark:bg-[#1D0002]"
                >
                    <div class="flex h-full items-center justify-center p-8">
                        <div class="text-center translate-y-0 opacity-100 transition-all duration-750 starting:translate-y-6 starting:opacity-0">
                            <!-- PWK Icon -->
                            <div class="mb-6 inline-flex h-24 w-24 items-center justify-center rounded-full bg-[#f53003] text-white shadow-lg dark:bg-[#FF4433]">
                                <Map :size="48" />
                            </div>

                            <h2 class="mb-2 text-xl font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                                Magister PWK
                            </h2>
                            <p class="mb-6 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                Perencanaan Wilayah dan Kota
                            </p>

                            <!-- Program Info Cards -->
                            <div class="grid grid-cols-2 gap-4 text-xs">
                                <div class="rounded-lg bg-white/60 p-4 shadow-sm dark:bg-black/20">
                                    <BookOpen class="mx-auto mb-2 h-5 w-5 text-[#f53003] dark:text-[#FF4433]" />
                                    <div class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">4 Semester</div>
                                    <div class="text-[#706f6c] dark:text-[#A1A09A]">Durasi</div>
                                </div>
                                <div class="rounded-lg bg-white/60 p-4 shadow-sm dark:bg-black/20">
                                    <Award class="mx-auto mb-2 h-5 w-5 text-[#f53003] dark:text-[#FF4433]" />
                                    <div class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">Terakreditasi</div>
                                    <div class="text-[#706f6c] dark:text-[#A1A09A]">Unggul</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="absolute inset-0 rounded-t-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:overflow-hidden lg:rounded-t-none lg:rounded-r-lg dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                    />
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
