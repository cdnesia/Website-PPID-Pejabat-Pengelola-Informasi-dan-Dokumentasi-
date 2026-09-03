<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import api from '@/lib/axios';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import ShareButtons from '@/components/shared/ShareButtons.vue';
import { Building2, Calendar, Download, FileText, Info, LayoutGrid, Search } from '@lucide/vue';

const route = useRoute();
const information = ref(null);
const otherInformations = ref([]);
const loading = ref(true);

onMounted(loadInformation);
watch(() => route.params.slug, loadInformation);

async function loadInformation() {
    loading.value = true;
    try {
        const { data } = await api.get(`/informations/${route.params.slug}`);
        information.value = data.data;
        document.title = `${information.value.title} — Informasi Publik — PPID UM Jambi`;

        const type = information.value.category?.type;
        const endpoint = type ? `/informations/type/${type}` : '/informations';
        const { data: listData } = await api.get(endpoint);
        otherInformations.value = listData.data.filter((item) => item.slug !== route.params.slug).slice(0, 5);
    } finally {
        loading.value = false;
    }
}

const categoryTabs = [
    { value: '', label: 'Semua Informasi' },
    { value: 'berkala', label: 'Informasi Berkala' },
    { value: 'serta_merta', label: 'Informasi Serta-Merta' },
    { value: 'setiap_saat', label: 'Informasi Setiap Saat' },
    { value: 'dikecualikan', label: 'Informasi Dikecualikan' },
];

const quickLinks = [
    { to: '/permohonan/buat', label: 'Ajukan Permohonan', icon: FileText },
    { to: '/permohonan/lacak', label: 'Lacak Permohonan', icon: Search },
];

const publishedDate = computed(() =>
    information.value ? new Date(information.value.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '',
);
</script>

<template>
    <PublicLayout>
        <section class="border-b border-border bg-gradient-to-b from-primary/10 via-secondary/5 to-background">
            <div class="mx-auto max-w-3xl px-4 py-12 text-center sm:py-16">
                <div v-if="loading" class="text-sm text-muted-foreground">Memuat informasi...</div>
                <div v-else-if="!information" class="text-sm text-muted-foreground">Informasi tidak ditemukan.</div>
                <div v-else>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold tracking-wide text-primary uppercase">
                        <LayoutGrid class="h-3.5 w-3.5" />
                        Informasi Publik
                    </span>
                    <h1 class="mt-4 text-2xl leading-tight font-bold tracking-tight text-foreground sm:text-4xl">{{ information.title }}</h1>

                    <div class="mt-5 flex flex-wrap items-center justify-center gap-2 text-xs text-muted-foreground">
                        <Badge variant="secondary">{{ information.category?.name }}</Badge>
                        <span class="flex items-center gap-1.5 rounded-full border border-border bg-background/70 px-3 py-1">
                            <Calendar class="h-3.5 w-3.5" />
                            {{ publishedDate }}
                        </span>
                        <span v-if="information.work_unit" class="flex items-center gap-1.5 rounded-full border border-border bg-background/70 px-3 py-1">
                            <Building2 class="h-3.5 w-3.5" />
                            {{ information.work_unit.name }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <div v-if="information" class="mx-auto max-w-6xl px-4 py-12">
            <div class="grid gap-8 lg:grid-cols-3 lg:gap-12">
                <article class="min-w-0 lg:col-span-2">
                    <div v-if="information.content" class="max-w-none text-base leading-relaxed whitespace-pre-line text-foreground">
                        {{ information.content }}
                    </div>
                    <div v-else class="flex items-start gap-3 rounded-xl border border-border bg-muted/40 p-4 text-sm text-muted-foreground">
                        <Info class="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground" />
                        <p>Tidak ada keterangan untuk informasi ini.</p>
                    </div>

                    <div v-if="information.file_url" class="mt-8">
                        <h2 class="mb-3 text-sm font-semibold text-foreground">Dokumen Lampiran</h2>
                        <div class="flex items-center gap-3 rounded-xl border border-border bg-card p-3 shadow-sm">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <FileText class="h-5 w-5" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-foreground">Tautan berkas lampiran</p>
                                <p class="truncate text-xs text-muted-foreground">{{ information.file_url }}</p>
                            </div>
                            <a
                                :href="information.file_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-md border border-input px-3 py-1.5 text-sm font-medium text-foreground transition-colors hover:border-primary/40 hover:bg-muted"
                            >
                                <Download class="h-4 w-4" />
                                Buka Tautan
                            </a>
                        </div>
                    </div>

                    <div class="mt-10 border-t border-border pt-6">
                        <ShareButtons :title="information.title" />
                    </div>
                </article>

                <aside class="flex flex-col gap-6 lg:col-span-1">
                    <div class="rounded-2xl border border-border bg-card p-5 shadow-sm">
                        <h2 class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <FileText class="h-4 w-4 text-primary" />
                            Informasi Lainnya
                        </h2>
                        <div v-if="otherInformations.length" class="mt-4 flex flex-col gap-4">
                            <RouterLink
                                v-for="item in otherInformations"
                                :key="item.id"
                                :to="`/informasi-publik/${item.slug}`"
                                class="group flex items-start gap-3"
                            >
                                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                    <FileText class="h-5 w-5" />
                                </span>
                                <div class="min-w-0">
                                    <p class="line-clamp-2 text-sm font-medium text-foreground transition-colors group-hover:text-primary">
                                        {{ item.title }}
                                    </p>
                                    <p class="mt-1 text-xs text-muted-foreground">
                                        {{ new Date(item.published_at).toLocaleDateString('id-ID') }}
                                    </p>
                                </div>
                            </RouterLink>
                        </div>
                        <p v-else class="mt-4 text-sm text-muted-foreground">Belum ada informasi lain.</p>
                    </div>

                    <div class="rounded-2xl border border-border bg-card p-5 shadow-sm">
                        <h2 class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <LayoutGrid class="h-4 w-4 text-primary" />
                            Kategori Informasi
                        </h2>
                        <div class="mt-4 flex flex-col gap-1">
                            <RouterLink
                                v-for="tab in categoryTabs"
                                :key="tab.value"
                                :to="{ path: '/informasi-publik', query: tab.value ? { type: tab.value } : {} }"
                                class="rounded-md px-3 py-2 text-sm text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            >
                                {{ tab.label }}
                            </RouterLink>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-border bg-card p-5 shadow-sm">
                        <h2 class="text-sm font-semibold text-foreground">Layanan Cepat</h2>
                        <div class="mt-4 flex flex-col gap-2">
                            <RouterLink v-for="link in quickLinks" :key="link.to" :to="link.to">
                                <Button variant="outline" size="sm" class="w-full justify-start">
                                    <component :is="link.icon" class="h-4 w-4" />
                                    {{ link.label }}
                                </Button>
                            </RouterLink>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </PublicLayout>
</template>
