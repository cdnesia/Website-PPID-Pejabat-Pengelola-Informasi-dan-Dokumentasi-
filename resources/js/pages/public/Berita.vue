<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import api from '@/lib/axios';
import PublicLayout from '@/layouts/PublicLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import EmptyState from '@/components/shared/EmptyState.vue';
import Pagination from '@/components/shared/Pagination.vue';
import { ArrowRight, Calendar, Eye, Newspaper } from '@lucide/vue';

const news = ref([]);
const loading = ref(true);
const page = ref(1);
const meta = ref(null);
const gridTop = ref(null);

async function loadNews() {
    loading.value = true;
    try {
        const { data } = await api.get('/news', { params: { page: page.value } });
        news.value = data.data;
        meta.value = data.meta;
    } finally {
        loading.value = false;
    }
}

function changePage(newPage) {
    page.value = newPage;
    loadNews();
    gridTop.value?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

onMounted(loadNews);

function formatDate(date) {
    return date ? new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '';
}
</script>

<template>
    <PublicLayout>
        <PageHeader
            title="Berita &amp; Pengumuman"
            subtitle="Informasi terkini seputar kegiatan, kebijakan, dan pengumuman resmi PPID."
        />
        <div class="mx-auto max-w-6xl px-4 py-10">
            <p v-if="!loading && meta" ref="gridTop" class="border-b border-border pb-3 text-xs text-muted-foreground">
                Menampilkan {{ meta.from ?? 0 }}–{{ meta.to ?? 0 }} dari {{ meta.total }} berita &amp; pengumuman
            </p>

            <div v-if="loading" class="mt-8 text-sm text-muted-foreground">Memuat data...</div>
            <EmptyState v-else-if="news.length === 0" :icon="Newspaper" message="Belum ada berita atau pengumuman." class="mt-8" />
            <div v-else class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <RouterLink
                    v-for="(item, index) in news"
                    :key="item.id"
                    v-reveal="(index % 3) * 100"
                    :to="`/berita/${item.slug}`"
                    class="group flex h-full flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                >
                    <div class="relative aspect-[16/10] w-full overflow-hidden bg-muted">
                        <img
                            v-if="item.thumbnail"
                            :src="item.thumbnail"
                            :alt="item.title"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        >
                        <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/5 via-secondary/5 to-accent/5">
                            <Newspaper class="h-10 w-10 text-primary/20" />
                        </div>
                        <span
                            v-if="item.category"
                            class="absolute top-3 left-3 rounded-full bg-background/90 px-2.5 py-1 text-[11px] font-semibold tracking-wide text-primary uppercase shadow-sm backdrop-blur"
                        >
                            {{ item.category }}
                        </span>
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex items-center gap-3 text-xs text-muted-foreground">
                            <span class="flex items-center gap-1.5">
                                <Calendar class="h-3.5 w-3.5" />
                                {{ formatDate(item.published_at) }}
                            </span>
                            <span v-if="item.view_count" class="flex items-center gap-1.5">
                                <Eye class="h-3.5 w-3.5" />
                                {{ item.view_count }}
                            </span>
                        </div>

                        <h3 class="mt-2 line-clamp-2 text-base leading-snug font-semibold text-foreground transition-colors group-hover:text-primary">
                            {{ item.title }}
                        </h3>
                        <p class="mt-1.5 line-clamp-2 text-sm text-muted-foreground">{{ item.excerpt }}</p>

                        <span
                            class="mt-auto flex items-center gap-1 pt-4 text-sm font-medium text-primary opacity-0 transition-all duration-200 group-hover:translate-x-0.5 group-hover:opacity-100"
                        >
                            Baca selengkapnya
                            <ArrowRight class="h-3.5 w-3.5" />
                        </span>
                    </div>
                </RouterLink>
            </div>

            <Pagination
                v-if="!loading && meta"
                :current-page="meta.current_page"
                :last-page="meta.last_page"
                class="mt-10"
                @update:page="changePage"
            />
        </div>
    </PublicLayout>
</template>
