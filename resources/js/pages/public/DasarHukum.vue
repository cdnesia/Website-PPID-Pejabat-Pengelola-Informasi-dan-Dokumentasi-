<script setup>
import { onMounted, ref } from 'vue';
import api from '@/lib/axios';
import PublicLayout from '@/layouts/PublicLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import { Scale } from '@lucide/vue';

const page = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const { data } = await api.get('/pages/dasar-hukum');
        page.value = data.data;
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <PublicLayout>
        <PageHeader v-if="page" :title="page.title" :subtitle="page.subtitle" />
        <div class="mx-auto max-w-3xl px-4 py-12">
            <p v-if="loading" class="text-sm text-muted-foreground">Memuat...</p>
            <div v-else-if="page" class="flex flex-col divide-y divide-border rounded-2xl border border-border bg-card shadow-sm">
                <div
                    v-for="(item, index) in page.content.items"
                    :key="item.title"
                    v-reveal="index * 70"
                    class="flex items-start gap-4 p-5"
                >
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <Scale class="h-4 w-4" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-foreground">{{ item.title }}</p>
                        <p class="mt-1 text-sm text-muted-foreground">{{ item.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
