<script setup>
import { ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { Button } from '@/components/ui/button';
import logoUrl from '@/assets/logo-ppid-umjambi.png';
import {
    LayoutDashboard,
    ClipboardList,
    FileText,
    Newspaper,
    Gavel,
    BarChart3,
    Users,
    Settings,
    FileStack,
    LogOut,
    Menu,
    PanelLeftClose,
    PanelLeftOpen,
    X,
} from '@lucide/vue';

const auth = useAuthStore();
const mobileOpen = ref(false);
const sidebarOpen = ref(true);

const navItems = [
    { to: '/admin/dashboard', label: 'Dashboard', icon: LayoutDashboard },
    { to: '/admin/permohonan', label: 'Permohonan', icon: ClipboardList },
    { to: '/admin/dokumen', label: 'Dokumen', icon: FileText },
    { to: '/admin/berita', label: 'Berita', icon: Newspaper },
    { to: '/admin/halaman-profil', label: 'Halaman Profil', icon: FileStack },
    { to: '/admin/keberatan', label: 'Keberatan', icon: Gavel },
    { to: '/admin/laporan', label: 'Laporan', icon: BarChart3 },
    { to: '/admin/pengguna', label: 'Pengguna', icon: Users },
    { to: '/admin/pengaturan', label: 'Pengaturan', icon: Settings },
];

const initials = (name) =>
    (name ?? '')
        .split(' ')
        .map((part) => part[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
</script>

<template>
    <div class="flex min-h-screen">
        <aside
            class="sticky top-0 hidden h-screen shrink-0 overflow-hidden border-r border-border bg-card transition-[width] duration-200 lg:flex lg:flex-col"
            :class="sidebarOpen ? 'lg:w-64' : 'lg:w-0 lg:border-r-0'"
        >
            <div class="flex h-full w-64 flex-col">
                <div class="border-b border-border p-4">
                    <RouterLink to="/" class="flex items-center">
                        <img :src="logoUrl" alt="PPID Universitas Muhammadiyah Jambi" class="h-9 w-auto">
                    </RouterLink>
                </div>
                <nav class="flex flex-1 flex-col gap-1 overflow-y-auto p-3">
                    <RouterLink
                        v-for="item in navItems"
                        :key="item.to"
                        :to="item.to"
                        class="flex items-center gap-2.5 rounded-md px-3 py-2 text-sm font-medium whitespace-nowrap text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                        active-class="bg-primary text-primary-foreground shadow-sm hover:bg-primary hover:text-primary-foreground"
                    >
                        <component :is="item.icon" class="h-4 w-4 shrink-0" />
                        {{ item.label }}
                    </RouterLink>
                </nav>
            </div>
        </aside>

        <div v-if="mobileOpen" class="fixed inset-0 z-40 flex lg:hidden">
            <div class="absolute inset-0 bg-black/40" @click="mobileOpen = false" />
            <aside class="relative flex w-64 flex-col bg-card shadow-xl">
                <div class="flex items-center justify-between border-b border-border p-4">
                    <RouterLink to="/" class="flex items-center" @click="mobileOpen = false">
                        <img :src="logoUrl" alt="PPID Universitas Muhammadiyah Jambi" class="h-9 w-auto">
                    </RouterLink>
                    <button type="button" class="rounded-md p-1.5 hover:bg-muted" @click="mobileOpen = false">
                        <X class="h-5 w-5" />
                    </button>
                </div>
                <nav class="flex flex-1 flex-col gap-1 p-3">
                    <RouterLink
                        v-for="item in navItems"
                        :key="item.to"
                        :to="item.to"
                        class="flex items-center gap-2.5 rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                        active-class="bg-primary text-primary-foreground shadow-sm hover:bg-primary hover:text-primary-foreground"
                        @click="mobileOpen = false"
                    >
                        <component :is="item.icon" class="h-4 w-4 shrink-0" />
                        {{ item.label }}
                    </RouterLink>
                </nav>
            </aside>
        </div>

        <div class="flex flex-1 flex-col">
            <header class="sticky top-0 z-30 flex items-center justify-between gap-4 border-b border-border bg-card/80 px-4 py-3.5 backdrop-blur-md sm:px-6">
                <button
                    type="button"
                    class="hidden h-9 w-9 items-center justify-center rounded-md text-foreground hover:bg-muted lg:inline-flex"
                    :aria-label="sidebarOpen ? 'Sembunyikan sidebar' : 'Tampilkan sidebar'"
                    @click="sidebarOpen = !sidebarOpen"
                >
                    <component :is="sidebarOpen ? PanelLeftClose : PanelLeftOpen" class="h-5 w-5" />
                </button>

                <button
                    type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md text-foreground hover:bg-muted lg:hidden"
                    aria-label="Buka menu"
                    @click="mobileOpen = true"
                >
                    <Menu class="h-5 w-5" />
                </button>

                <div class="ml-auto flex items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-medium text-foreground">{{ auth.user?.name }}</p>
                        <p class="text-xs text-muted-foreground">{{ auth.user?.email }}</p>
                    </div>
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-sm font-semibold text-primary">
                        {{ initials(auth.user?.name) }}
                    </span>
                    <Button size="sm" variant="outline" @click="auth.logout()">
                        <LogOut class="h-4 w-4" />
                        <span class="hidden sm:inline">Keluar</span>
                    </Button>
                </div>
            </header>
            <main class="flex-1 bg-muted/30 p-4 sm:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
