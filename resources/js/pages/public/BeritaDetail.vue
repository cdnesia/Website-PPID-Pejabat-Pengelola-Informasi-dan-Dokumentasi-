<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import api from '@/lib/axios';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import ShareButtons from '@/components/shared/ShareButtons.vue';
import { Calendar, Eye, FileText, Newspaper, Search, User } from '@lucide/vue';

const route = useRoute();
const news = ref(null);
const otherNews = ref([]);
const loading = ref(true);

onMounted(loadNews);
watch(() => route.params.slug, loadNews);

async function loadNews() {
    loading.value = true;
    try {
        const [{ data }, { data: listData }] = await Promise.all([
            api.get(`/news/${route.params.slug}`),
            api.get('/news', { params: { page: 1 } }),
        ]);

        news.value = data.data;
        otherNews.value = listData.data.filter((item) => item.slug !== route.params.slug).slice(0, 5);
        document.title = `${news.value.title} — Berita — PPID UM Jambi`;
    } finally {
        loading.value = false;
    }
}

const publishedDate = computed(() =>
    news.value ? new Date(news.value.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '',
);

const quickLinks = [
    { to: '/permohonan/buat', label: 'Ajukan Permohonan', icon: FileText },
    { to: '/permohonan/lacak', label: 'Lacak Permohonan', icon: Search },
];
</script>

<template>
    <PublicLayout>
        <section class="border-b border-border bg-gradient-to-b from-primary/10 via-secondary/5 to-background">
            <div class="mx-auto max-w-3xl px-4 py-12 text-center sm:py-16">
                <div v-if="loading" class="text-sm text-muted-foreground">Memuat berita...</div>
                <div v-else-if="!news" class="text-sm text-muted-foreground">Berita tidak ditemukan.</div>
                <div v-else>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold tracking-wide text-primary uppercase">
                        <Newspaper class="h-3.5 w-3.5" />
                        Berita &amp; Pengumuman
                    </span>
                    <h1 class="mt-4 text-2xl leading-tight font-bold tracking-tight text-foreground sm:text-4xl">{{ news.title }}</h1>

                    <div class="mt-5 flex flex-wrap items-center justify-center gap-2 text-xs text-muted-foreground">
                        <Badge v-if="news.category" variant="secondary">{{ news.category }}</Badge>
                        <span class="flex items-center gap-1.5 rounded-full border border-border bg-background/70 px-3 py-1">
                            <Calendar class="h-3.5 w-3.5" />
                            {{ publishedDate }}
                        </span>
                        <span v-if="news.author" class="flex items-center gap-1.5 rounded-full border border-border bg-background/70 px-3 py-1">
                            <User class="h-3.5 w-3.5" />
                            {{ news.author }}
                        </span>
                        <span class="flex items-center gap-1.5 rounded-full border border-border bg-background/70 px-3 py-1">
                            <Eye class="h-3.5 w-3.5" />
                            {{ news.view_count }} kali dilihat
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <div v-if="news" class="mx-auto max-w-6xl px-4 py-12">
            <div class="grid gap-8 lg:grid-cols-3 lg:gap-12">
                <article class="min-w-0 lg:col-span-2">
                    <img
                        v-if="news.thumbnail"
                        :src="news.thumbnail"
                        :alt="news.title"
                        class="aspect-video w-full rounded-2xl object-cover shadow-sm"
                    />

                    <div
                        class="prose-content max-w-none text-base leading-relaxed text-foreground"
                        :class="news.thumbnail ? 'mt-8' : ''"
                        v-html="news.content"
                    />

                    <div class="mt-10 border-t border-border pt-6">
                        <ShareButtons :title="news.title" />
                    </div>
                </article>

                <aside class="flex flex-col gap-6 lg:col-span-1">
                    <div class="rounded-2xl border border-border bg-card p-5 shadow-sm">
                        <h2 class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <Newspaper class="h-4 w-4 text-primary" />
                            Berita Lainnya
                        </h2>
                        <div v-if="otherNews.length" class="mt-4 flex flex-col gap-4">
                            <RouterLink
                                v-for="item in otherNews"
                                :key="item.id"
                                :to="`/berita/${item.slug}`"
                                class="group flex items-start gap-3"
                            >
                                <img
                                    v-if="item.thumbnail"
                                    :src="item.thumbnail"
                                    :alt="item.title"
                                    class="h-14 w-14 shrink-0 rounded-lg object-cover"
                                />
                                <span v-else class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                    <Newspaper class="h-5 w-5" />
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
                        <p v-else class="mt-4 text-sm text-muted-foreground">Belum ada berita lain.</p>
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
