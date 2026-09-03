<script setup>
import { onMounted, ref } from 'vue';
import api from '@/lib/axios';
import PublicLayout from '@/layouts/PublicLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import EmptyState from '@/components/shared/EmptyState.vue';
import { ImageOff } from '@lucide/vue';

const page = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const { data } = await api.get('/pages/alur-layanan');
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
                    v-reveal
                    :src="page.image_url"
                    :alt="page.title"
                    class="w-full rounded-2xl border border-border object-contain shadow-sm"
                />
                <EmptyState
                    v-else
                    :icon="ImageOff"
                    message="Gambar alur layanan belum tersedia. Admin dapat mengunggahnya melalui menu Halaman Profil."
                />
            </template>
        </div>
    </PublicLayout>
</template>
