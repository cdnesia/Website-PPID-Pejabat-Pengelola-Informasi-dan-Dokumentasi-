<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import api from '@/lib/axios';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '@/components/ui/card';
import AnimatedCounter from '@/components/shared/AnimatedCounter.vue';
import EmptyState from '@/components/shared/EmptyState.vue';
import { informationTypes } from '@/lib/informationTypes';
import { CalendarClock, ClipboardCheck, ArrowRight, Clock3, FileStack, FileText, LayoutGrid, Newspaper, Search, Wifi } from '@lucide/vue';

const router = useRouter();
const categories = ref([]);
const news = ref([]);
const informationCount = ref(0);
const loading = ref(true);
const loadError = ref(false);
const searchQuery = ref('');

function submitSearch() {
    router.push({ path: '/informasi-publik', query: searchQuery.value ? { q: searchQuery.value } : {} });
}

onMounted(async () => {
    try {
        const [categoriesRes, newsRes, informationsRes] = await Promise.all([
            api.get('/categories'),
            api.get('/news', { params: { page: 1 } }),
            api.get('/informations', { params: { page: 1 } }),
        ]);

        categories.value = categoriesRes.data.data;
        news.value = newsRes.data.data.slice(0, 3);
        informationCount.value = informationsRes.data.meta?.total ?? informationsRes.data.data.length;
    } catch {
        loadError.value = true;
    } finally {
        loading.value = false;
    }
});

const quickLinks = [
    {
        to: '/permohonan/buat',
        title: 'Permohonan Informasi',
        description: 'Ajukan permohonan informasi publik secara online.',
        icon: FileText,
    },
    {
        to: '/informasi-publik',
        title: 'Daftar Informasi Publik',
        description: 'Telusuri informasi berkala, serta-merta, setiap saat, dan dikecualikan.',
        icon: LayoutGrid,
    },
    {
        to: '/permohonan/lacak',
        title: 'Lacak Permohonan',
        description: 'Pantau status permohonan Anda dengan nomor permohonan.',
        icon: Search,
    },
];

const stats = [
    { icon: FileStack, value: () => informationCount.value, numeric: true, label: 'Informasi Publik Tersedia' },
    { icon: LayoutGrid, value: () => categories.value.length, numeric: true, label: 'Kategori Informasi' },
    { icon: Clock3, value: () => '10 hari', numeric: false, label: 'Standar Waktu Respons' },
];
</script>

<template>
    <PublicLayout>
        <section class="relative overflow-hidden border-b border-border bg-gradient-to-br from-primary via-primary to-[#5c1621] text-primary-foreground">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top,_rgba(255,255,255,0.10),_transparent_60%)]" />
            <div class="relative mx-auto max-w-6xl px-4 py-16 text-center sm:py-20">
                <span
                    class="hero-fade-up inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-medium ring-1 ring-white/20"
                    style="animation-delay: 0ms"
                >
                    <ClipboardCheck class="h-3.5 w-3.5" />
                    Keterbukaan Informasi Publik
                </span>
                <h1 class="hero-fade-up mx-auto mt-6 max-w-3xl text-2xl font-bold tracking-tight sm:text-4xl" style="animation-delay: 80ms">
                    Universitas Muhammadiyah Jambi
                </h1>
                <p
                    class="hero-fade-up mx-auto mt-4 max-w-2xl text-sm text-primary-foreground/85 sm:text-base"
                    style="animation-delay: 160ms"
                >
                    Mewujudkan keterbukaan informasi publik sesuai UU No. 14 Tahun 2008, demi transparansi dan
                    akuntabilitas layanan kepada masyarakat.
                </p>
                <form class="hero-fade-up mx-auto mt-8 flex max-w-lg gap-2" style="animation-delay: 240ms" @submit.prevent="submitSearch">
                    <div class="relative min-w-0 flex-1">
                        <Search class="pointer-events-none absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            v-model="searchQuery"
                            placeholder="Cari informasi publik..."
                            class="h-12 border-0 bg-white pl-10 text-foreground shadow-md placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-secondary"
                        />
                    </div>
                    <Button type="submit" size="lg" variant="secondary" class="h-12 shrink-0 px-6 shadow-md">Cari</Button>
                </form>

                <div
                    class="hero-fade-up mt-4 flex flex-wrap items-center justify-center gap-x-4 gap-y-1.5 text-xs text-primary-foreground/75"
                    style="animation-delay: 300ms"
                >
                    <span class="flex items-center gap-1.5">
                        <CalendarClock class="h-3.5 w-3.5" />
                        Senin–Jumat, 08.00–16.00 WIB
                    </span>
                    <span class="flex items-center gap-1.5">
                        <Wifi class="h-3.5 w-3.5" />
                        Permohonan online 24/7
                    </span>
                </div>

                <div class="hero-fade-up mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row" style="animation-delay: 360ms">
                    <RouterLink to="/permohonan/buat">
                        <Button size="lg" variant="secondary" class="w-full sm:w-auto">
                            Ajukan Permohonan Informasi
                            <ArrowRight class="h-4 w-4" />
                        </Button>
                    </RouterLink>
                    <RouterLink to="/permohonan/lacak">
                        <Button size="lg" variant="outline" class="w-full border-white/25 bg-transparent text-primary-foreground hover:bg-white/10 sm:w-auto">
                            Cek Status Permohonan
                        </Button>
                    </RouterLink>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-16">
            <div v-reveal class="mb-8 text-center">
                <h2 class="text-2xl font-semibold">Layanan Utama</h2>
                <p class="mt-2 text-sm text-muted-foreground">Akses cepat ke layanan yang paling sering digunakan.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <RouterLink
                    v-for="(link, index) in quickLinks"
                    :key="link.to"
                    v-reveal="index * 100"
                    :to="link.to"
                    class="group"
                >
                    <Card class="h-full transition-all duration-200 hover:-translate-y-1 hover:border-primary/40 hover:shadow-md">
                        <CardHeader>
                            <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary/10 text-primary transition-colors group-hover:bg-primary group-hover:text-primary-foreground">
                                <component :is="link.icon" class="h-5 w-5" />
                            </span>
                            <CardTitle class="mt-3">{{ link.title }}</CardTitle>
                            <CardDescription>{{ link.description }}</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <span class="inline-flex items-center gap-1 text-sm font-medium text-primary">
                                Buka
                                <ArrowRight class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1" />
                            </span>
                        </CardContent>
                    </Card>
                </RouterLink>
            </div>
        </section>

        <section class="bg-card py-16">
            <div class="mx-auto max-w-6xl px-4">
                <div v-reveal class="mb-8 text-center">
                    <h2 class="text-2xl font-semibold">Jenis Informasi Publik</h2>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Klasifikasi informasi sesuai UU No. 14 Tahun 2008 tentang Keterbukaan Informasi Publik.
                    </p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <RouterLink
                        v-for="(type, index) in informationTypes"
                        :key="type.value"
                        v-reveal="index * 100"
                        :to="{ path: '/informasi-publik', query: { type: type.value } }"
                        class="group relative flex flex-col overflow-hidden rounded-2xl border border-border bg-background p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md"
                    >
                        <span class="absolute inset-x-0 top-0 h-[3px]" :class="type.bar" />
                        <span class="flex h-11 w-11 items-center justify-center rounded-lg" :class="[type.soft, type.text]">
                            <component :is="type.icon" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-3 text-base font-semibold text-foreground">{{ type.label }}</h3>
                        <p class="mt-1.5 text-sm text-muted-foreground">{{ type.description }}</p>
                    </RouterLink>
                </div>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto max-w-6xl px-4">
                <div class="grid gap-6 sm:grid-cols-3">
                    <div
                        v-for="(stat, index) in stats"
                        :key="stat.label"
                        v-reveal="index * 100"
                        class="flex flex-col items-center gap-3 rounded-xl border border-border bg-background p-8 text-center shadow-sm transition-transform duration-200 hover:-translate-y-1"
                    >
                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-accent/10 text-accent">
                            <component :is="stat.icon" class="h-6 w-6" />
                        </span>
                        <p class="text-3xl font-bold text-primary">
                            <AnimatedCounter v-if="stat.numeric" :value="stat.value()" />
                            <template v-else>{{ stat.value() }}</template>
                        </p>
                        <p class="text-sm text-muted-foreground">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-16">
            <div v-reveal class="mb-6 flex items-center justify-between">
                <h2 class="flex items-center gap-2 text-xl font-semibold">
                    <Newspaper class="h-5 w-5 text-primary" />
                    Berita &amp; Pengumuman Terkini
                </h2>
                <RouterLink to="/berita" class="inline-flex items-center gap-1 text-sm font-medium text-primary hover:underline">
                    Lihat semua
                    <ArrowRight class="h-3.5 w-3.5" />
                </RouterLink>
            </div>
            <div v-if="loading" class="grid gap-4 sm:grid-cols-3">
                <div v-for="i in 3" :key="i" class="h-40 animate-pulse rounded-xl border border-border bg-muted/40" />
            </div>
            <EmptyState
                v-else-if="loadError"
                :icon="Newspaper"
                message="Berita belum dapat dimuat saat ini. Silakan muat ulang halaman."
            />
            <EmptyState v-else-if="news.length === 0" :icon="Newspaper" message="Belum ada berita atau pengumuman terbaru." />
            <div v-else class="grid gap-4 sm:grid-cols-3">
                <RouterLink
                    v-for="(item, index) in news"
                    :key="item.id"
                    v-reveal="index * 100"
                    :to="`/berita/${item.slug}`"
                    class="group"
                >
                    <Card class="h-full transition-all duration-200 hover:-translate-y-1 hover:border-primary/40 hover:shadow-md">
                        <CardHeader>
                            <CardTitle class="text-base transition-colors group-hover:text-primary">{{ item.title }}</CardTitle>
                            <CardDescription>{{ item.excerpt }}</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <span class="inline-flex items-center gap-1 text-sm font-medium text-primary">
                                Baca selengkapnya
                                <ArrowRight class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1" />
                            </span>
                        </CardContent>
                    </Card>
                </RouterLink>
            </div>
        </section>
    </PublicLayout>
</template>
