<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { RouterLink, useRoute, useRouter } from 'vue-router';
import api from '@/lib/axios';
import PublicLayout from '@/layouts/PublicLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import EmptyState from '@/components/shared/EmptyState.vue';
import Pagination from '@/components/shared/Pagination.vue';
import { Input } from '@/components/ui/input';
import { getInformationType, informationTypes } from '@/lib/informationTypes';
import { ArrowRight, Calendar, FileText, Paperclip, Search } from '@lucide/vue';

const route = useRoute();
const router = useRouter();

const tabs = [{ value: '', label: 'Semua' }, ...informationTypes.map(({ value, label }) => ({ value, label }))];

const activeType = ref(route.query.type ?? '');
const search = ref(route.query.q ?? '');
const informations = ref([]);
const loading = ref(true);
const page = ref(1);
const meta = ref(null);
const gridTop = ref(null);

async function loadInformations() {
    loading.value = true;
    try {
        const endpoint = activeType.value ? `/informations/type/${activeType.value}` : '/informations';
        const { data } = await api.get(endpoint, { params: { q: search.value || undefined, page: page.value } });
        informations.value = data.data;
        meta.value = data.meta;
    } finally {
        loading.value = false;
    }
}

function selectTab(type) {
    activeType.value = type;
    router.replace({ query: { ...route.query, type: type || undefined } });
}

function changePage(newPage) {
    page.value = newPage;
    loadInformations();
    gridTop.value?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

watch([activeType, search], () => {
    page.value = 1;
    loadInformations();
});
onMounted(loadInformations);

const typeLabel = computed(() => (type) => tabs.find((tab) => tab.value === type)?.label ?? type);
const typeStyle = (type) => getInformationType(type);

function formatDate(date) {
    return date ? new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '';
}
</script>

<template>
    <PublicLayout>
        <PageHeader
            title="Daftar Informasi Publik"
            subtitle="Informasi berkala, serta-merta, setiap saat, dan dikecualikan sesuai UU Keterbukaan Informasi Publik."
        />
        <div class="mx-auto max-w-6xl px-4 py-10">
            <div class="flex flex-wrap items-end justify-between gap-4 border-b border-border pb-3">
                <nav class="flex flex-wrap gap-x-6 gap-y-2">
                    <button
                        v-for="tab in tabs"
                        :key="tab.value"
                        type="button"
                        class="-mb-3 border-b-2 pb-3 text-sm font-medium transition-colors"
                        :class="activeType === tab.value ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'"
                        @click="selectTab(tab.value)"
                    >
                        {{ tab.label }}
                    </button>
                </nav>

                <div class="relative w-full max-w-[220px] sm:w-auto">
                    <Search class="pointer-events-none absolute top-1/2 left-3 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="search" placeholder="Cari informasi..." class="h-9 pl-8 text-sm" />
                </div>
            </div>

            <p v-if="!loading && meta" ref="gridTop" class="mt-4 text-xs text-muted-foreground">
                Menampilkan {{ meta.from ?? 0 }}–{{ meta.to ?? 0 }} dari {{ meta.total }} informasi
                <template v-if="activeType">dalam kategori {{ typeLabel(activeType).toLowerCase() }}</template>
            </p>

            <div v-if="loading" class="mt-8 text-sm text-muted-foreground">Memuat data...</div>
            <EmptyState v-else-if="informations.length === 0" :icon="FileText" message="Belum ada informasi pada kategori ini." class="mt-8" />
            <div v-else class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <RouterLink
                    v-for="(item, index) in informations"
                    :key="item.id"
                    v-reveal="(index % 3) * 100"
                    :to="`/informasi-publik/${item.slug}`"
                    class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-border bg-card p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                >
                    <span class="absolute inset-y-0 left-0 w-[3px]" :class="typeStyle(item.category?.type).bar" />
                    <component
                        :is="typeStyle(item.category?.type).icon"
                        class="pointer-events-none absolute -top-3 -right-3 h-24 w-24 opacity-[0.06] transition-transform duration-300 group-hover:scale-110 group-hover:opacity-[0.1]"
                        :class="typeStyle(item.category?.type).watermark"
                    />

                    <div class="relative flex items-center gap-1.5 text-[11px] font-semibold tracking-wide uppercase" :class="typeStyle(item.category?.type).text">
                        <component :is="typeStyle(item.category?.type).icon" class="h-3.5 w-3.5" />
                        {{ typeLabel(item.category?.type) }}
                    </div>

                    <h3 class="relative mt-3 text-base leading-snug font-semibold text-foreground transition-colors group-hover:text-primary">
                        {{ item.title }}
                    </h3>
                    <p class="relative mt-1.5 line-clamp-2 text-sm text-muted-foreground">{{ item.description }}</p>

                    <div class="relative mt-auto flex items-center justify-between gap-2 border-t border-border/70 pt-3.5 text-xs text-muted-foreground">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center gap-1.5">
                                <Calendar class="h-3.5 w-3.5" />
                                {{ formatDate(item.published_at) }}
                            </span>
                            <span v-if="item.file_url" class="flex items-center gap-1 rounded-full border border-border px-2 py-0.5">
                                <Paperclip class="h-3 w-3" />
                                Lampiran
                            </span>
                        </div>
                        <ArrowRight class="h-3.5 w-3.5 -translate-x-1 text-primary opacity-0 transition-all duration-200 group-hover:translate-x-0 group-hover:opacity-100" />
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
