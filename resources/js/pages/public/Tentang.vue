<script setup>
import { onMounted, ref } from 'vue';
import api from '@/lib/axios';
import PublicLayout from '@/layouts/PublicLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';

const page = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const { data } = await api.get('/pages/tentang-ppid');
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
            <p v-else-if="page" v-reveal class="text-base leading-relaxed text-foreground">{{ page.content.body }}</p>
        </div>
    </PublicLayout>
</template>
