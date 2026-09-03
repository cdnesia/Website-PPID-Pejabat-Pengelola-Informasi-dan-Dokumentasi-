<script setup>
import { computed, onMounted, ref } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useSettingsStore } from '@/stores/settings';
import { Button } from '@/components/ui/button';
import NavDropdown from '@/components/layout/NavDropdown.vue';
import { isLinkActive } from '@/lib/nav';
import logoUrl from '@/assets/logo-ppid-umjambi.png';
import { Clock, LayoutDashboard, LogOut, Mail, Megaphone, Menu, MapPin, Phone, X } from '@lucide/vue';

const auth = useAuthStore();
const settingsStore = useSettingsStore();
const route = useRoute();
const mobileOpen = ref(false);

onMounted(() => settingsStore.fetchSettings());

const orgName = computed(() => settingsStore.settings?.org_name || 'PPID UM Jambi');
const orgEmail = computed(() => settingsStore.settings?.org_email || 'ppid@umjambi.ac.id');
const orgPhone = computed(() => settingsStore.settings?.org_phone || '(0741) 60825');
const orgAddress = computed(() => settingsStore.settings?.org_address || 'Kampus Universitas Muhammadiyah Jambi');
const showBanner = computed(() => settingsStore.settings?.banner_is_active && settingsStore.settings?.banner_text);

const profileLinks = [
    { to: '/tentang-ppid', label: 'Tentang PPID' },
    { to: '/visi-misi', label: 'Visi dan Misi' },
    { to: '/struktur-organisasi', label: 'Struktur Organisasi' },
    { to: '/tugas-fungsi', label: 'Tugas Pokok dan Fungsi' },
    { to: '/dasar-hukum', label: 'Dasar Hukum' },
];

const serviceLinks = [
    { to: '/permohonan/buat', label: 'Permohonan Informasi' },
    { to: '/permohonan/lacak', label: 'Cek Status Permohonan' },
    { to: '/keberatan', label: 'Ajukan Keberatan' },
    { to: '/alur-layanan', label: 'Alur Layanan' },
];

const navLinks = [
    { to: '/berita', label: 'Berita' },
    { to: '/kontak', label: 'Kontak' },
];

const mobileGroups = [
    { title: 'Profil PPID', links: profileLinks },
    { title: 'Layanan', links: serviceLinks },
];
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <div v-if="showBanner" class="bg-primary px-4 py-2 text-center text-xs font-medium text-primary-foreground sm:text-sm">
            <span class="mx-auto flex max-w-6xl items-center justify-center gap-2">
                <Megaphone class="h-3.5 w-3.5 shrink-0" />
                {{ settingsStore.settings.banner_text }}
            </span>
        </div>

        <header class="sticky top-0 z-40 border-b border-border bg-card/80 backdrop-blur-md">
            <div class="hidden border-b border-border bg-muted/40 md:block">
                <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-1.5 text-xs text-muted-foreground">
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-1.5">
                            <Mail class="h-3.5 w-3.5" />
                            {{ orgEmail }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <Phone class="h-3.5 w-3.5" />
                            {{ orgPhone }}
                        </span>
                    </div>
                    <span class="flex items-center gap-1.5">
                        <Clock class="h-3.5 w-3.5" />
                        Senin–Jumat, 08.00–16.00 WIB &middot; Permohonan online 24/7
                    </span>
                </div>
            </div>

            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3.5">
                <RouterLink to="/" class="flex items-center" @click="mobileOpen = false">
                    <img :src="logoUrl" alt="PPID Universitas Muhammadiyah Jambi" class="h-10 w-auto sm:h-11">
                </RouterLink>

                <nav class="hidden flex-1 items-stretch gap-1 self-stretch lg:flex">
                    <RouterLink
                        to="/"
                        class="flex items-center border-b-2 border-transparent px-3 text-sm font-semibold text-muted-foreground transition-colors hover:text-foreground"
                        active-class="border-primary text-primary hover:text-primary"
                    >
                        Beranda
                    </RouterLink>

                    <NavDropdown label="Profil" :links="profileLinks" />
                    <RouterLink
                        to="/informasi-publik"
                        class="flex items-center border-b-2 border-transparent px-3 text-sm font-semibold text-muted-foreground transition-colors hover:text-foreground"
                        active-class="border-primary text-primary hover:text-primary"
                    >
                        Informasi Publik
                    </RouterLink>
                    <NavDropdown label="Layanan" :links="serviceLinks" />

                    <RouterLink
                        v-for="link in navLinks"
                        :key="link.to"
                        :to="link.to"
                        class="flex items-center border-b-2 border-transparent px-3 text-sm font-semibold text-muted-foreground transition-colors hover:text-foreground"
                        active-class="border-primary text-primary hover:text-primary"
                    >
                        {{ link.label }}
                    </RouterLink>
                </nav>

                <div class="hidden items-center gap-2 lg:flex">
                    <template v-if="auth.isAuthenticated">
                        <RouterLink v-if="auth.isAdmin" to="/admin/dashboard">
                            <Button size="sm" variant="outline">
                                <LayoutDashboard class="h-4 w-4" />
                                Dashboard Admin
                            </Button>
                        </RouterLink>
                        <Button size="sm" variant="ghost" @click="auth.logout()">
                            <LogOut class="h-4 w-4" />
                            Keluar
                        </Button>
                    </template>
                    <template v-else>
                        <RouterLink to="/auth/login">
                            <Button size="sm" variant="outline">Masuk</Button>
                        </RouterLink>
                        <RouterLink to="/auth/register">
                            <Button size="sm">Daftar</Button>
                        </RouterLink>
                    </template>
                </div>

                <button
                    type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-md text-foreground hover:bg-muted lg:hidden"
                    aria-label="Buka menu"
                    @click="mobileOpen = !mobileOpen"
                >
                    <Menu v-if="!mobileOpen" class="h-5 w-5" />
                    <X v-else class="h-5 w-5" />
                </button>
            </div>

            <div v-if="mobileOpen" class="max-h-[calc(100vh-4rem)] overflow-y-auto border-t border-border bg-card px-4 pb-4 lg:hidden">
                <nav class="flex flex-col gap-1 pt-2">
                    <RouterLink
                        to="/"
                        class="rounded-md px-3 py-2.5 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground"
                        active-class="text-primary bg-primary/10"
                        @click="mobileOpen = false"
                    >
                        Beranda
                    </RouterLink>
                    <RouterLink
                        to="/informasi-publik"
                        class="rounded-md px-3 py-2.5 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground"
                        active-class="text-primary bg-primary/10"
                        @click="mobileOpen = false"
                    >
                        Informasi Publik
                    </RouterLink>

                    <div v-for="group in mobileGroups" :key="group.title" class="mt-2 border-t border-border pt-2">
                        <p class="px-3 pt-1 pb-1 text-xs font-semibold tracking-wide text-muted-foreground uppercase">{{ group.title }}</p>
                        <RouterLink
                            v-for="link in group.links"
                            :key="link.to"
                            :to="link.to"
                            class="block rounded-md px-3 py-2.5 pl-5 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground"
                            :class="isLinkActive(route, link.to) && 'text-primary bg-primary/10'"
                            @click="mobileOpen = false"
                        >
                            {{ link.label }}
                        </RouterLink>
                    </div>

                    <div class="mt-2 flex flex-col gap-1 border-t border-border pt-2">
                        <RouterLink
                            v-for="link in navLinks"
                            :key="link.to"
                            :to="link.to"
                            class="rounded-md px-3 py-2.5 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground"
                            active-class="text-primary bg-primary/10"
                            @click="mobileOpen = false"
                        >
                            {{ link.label }}
                        </RouterLink>
                    </div>
                </nav>
                <div class="mt-3 flex flex-col gap-2 border-t border-border pt-3">
                    <template v-if="auth.isAuthenticated">
                        <RouterLink v-if="auth.isAdmin" to="/admin/dashboard" @click="mobileOpen = false">
                            <Button size="sm" variant="outline" class="w-full">
                                <LayoutDashboard class="h-4 w-4" />
                                Dashboard Admin
                            </Button>
                        </RouterLink>
                        <Button size="sm" variant="ghost" class="w-full" @click="auth.logout(); mobileOpen = false">
                            <LogOut class="h-4 w-4" />
                            Keluar
                        </Button>
                    </template>
                    <template v-else>
                        <RouterLink to="/auth/login" @click="mobileOpen = false">
                            <Button size="sm" variant="outline" class="w-full">Masuk</Button>
                        </RouterLink>
                        <RouterLink to="/auth/register" @click="mobileOpen = false">
                            <Button size="sm" class="w-full">Daftar</Button>
                        </RouterLink>
                    </template>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t border-border bg-card">
            <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <RouterLink to="/" class="flex items-center">
                        <img :src="logoUrl" alt="PPID Universitas Muhammadiyah Jambi" class="h-11 w-auto">
                    </RouterLink>
                    <p class="mt-3 text-sm text-muted-foreground">
                        Mewujudkan keterbukaan informasi publik sesuai UU No. 14 Tahun 2008 demi transparansi dan
                        akuntabilitas layanan kepada masyarakat.
                    </p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-foreground">Layanan</p>
                    <nav class="mt-3 flex flex-col gap-2 text-sm text-muted-foreground">
                        <RouterLink to="/permohonan/buat" class="transition-colors hover:text-primary">Ajukan Permohonan</RouterLink>
                        <RouterLink to="/permohonan/lacak" class="transition-colors hover:text-primary">Lacak Permohonan</RouterLink>
                        <RouterLink to="/keberatan" class="transition-colors hover:text-primary">Ajukan Keberatan</RouterLink>
                        <RouterLink to="/informasi-publik" class="transition-colors hover:text-primary">Informasi Publik</RouterLink>
                    </nav>
                </div>

                <div>
                    <p class="text-sm font-semibold text-foreground">Tentang</p>
                    <nav class="mt-3 flex flex-col gap-2 text-sm text-muted-foreground">
                        <RouterLink to="/tentang-ppid" class="transition-colors hover:text-primary">Tentang PPID</RouterLink>
                        <RouterLink to="/dasar-hukum" class="transition-colors hover:text-primary">Dasar Hukum</RouterLink>
                        <RouterLink to="/alur-layanan" class="transition-colors hover:text-primary">Alur Layanan</RouterLink>
                        <RouterLink to="/faq" class="transition-colors hover:text-primary">FAQ</RouterLink>
                    </nav>
                </div>

                <div>
                    <p class="text-sm font-semibold text-foreground">Kontak</p>
                    <div class="mt-3 flex flex-col gap-2 text-sm text-muted-foreground">
                        <p class="flex items-start gap-2">
                            <MapPin class="mt-0.5 h-4 w-4 shrink-0" />
                            <span>{{ orgAddress }}</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <Phone class="h-4 w-4 shrink-0" />
                            <span>{{ orgPhone }}</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <Mail class="h-4 w-4 shrink-0" />
                            <span>{{ orgEmail }}</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="border-t border-border py-4 text-center text-sm text-muted-foreground">
                &copy; {{ new Date().getFullYear() }} {{ orgName }}. Keterbukaan Informasi Publik sesuai UU No. 14 Tahun 2008.
            </div>
        </footer>
    </div>
</template>
