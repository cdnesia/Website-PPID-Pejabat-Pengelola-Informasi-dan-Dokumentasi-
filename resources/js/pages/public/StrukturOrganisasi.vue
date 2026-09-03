<script setup>
import { onMounted, ref } from 'vue';
import api from '@/lib/axios';
import PublicLayout from '@/layouts/PublicLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';

const page = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const { data } = await api.get('/pages/struktur-organisasi');
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
            <template v-else-if="page">
                <img
                    v-if="page.image_url"
                    :src="page.image_url"
                    :alt="page.title"
                    class="w-full rounded-2xl border border-border object-contain shadow-sm"
                />

                <div
                    class="flex flex-col divide-y divide-border rounded-2xl border border-border bg-card shadow-sm"
                    :class="page.image_url ? 'mt-8' : ''"
                >
                    <div v-for="(item, index) in page.content.items" :key="item.title" class="flex items-start gap-4 p-5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-sm font-semibold text-primary">
                            {{ index + 1 }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-foreground">{{ item.title }}</p>
                            <p class="mt-1 text-sm text-muted-foreground">{{ item.description }}</p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </PublicLayout>
</template>
