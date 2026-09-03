<script setup>
import { onMounted, ref } from 'vue';
import api from '@/lib/axios';
import PublicLayout from '@/layouts/PublicLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import { ScrollText } from '@lucide/vue';

const page = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const { data } = await api.get('/pages/tugas-fungsi');
        page.value = data.data;
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <PublicLayout>
        <PageHeader v-if="page" :title="page.title" :subtitle="page.subtitle" />
        <div class="mx-auto max-w-4xl px-4 py-12">
            <p v-if="loading" class="text-sm text-muted-foreground">Memuat...</p>
            <div v-else-if="page" class="grid gap-4 sm:grid-cols-2">
                <div v-for="item in page.content.items" :key="item.title" class="rounded-2xl border border-border bg-card p-5 shadow-sm">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <ScrollText class="h-5 w-5" />
                    </span>
                    <p class="mt-3 text-sm font-semibold text-foreground">{{ item.title }}</p>
                    <p class="mt-1 text-sm text-muted-foreground">{{ item.description }}</p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
