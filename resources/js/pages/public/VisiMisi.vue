<script setup>
import { onMounted, ref } from 'vue';
import api from '@/lib/axios';
import PublicLayout from '@/layouts/PublicLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import { Target } from '@lucide/vue';

const page = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const { data } = await api.get('/pages/visi-misi');
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
                <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                    <p class="text-sm font-semibold text-foreground">Visi</p>
                    <p class="mt-2 text-base leading-relaxed text-muted-foreground">{{ page.content.visi }}</p>
                </div>

                <div class="mt-6">
                    <p class="text-sm font-semibold text-foreground">Misi</p>
                    <ul class="mt-3 flex flex-col gap-3">
                        <li
                            v-for="item in page.content.misi"
                            :key="item"
                            class="flex items-start gap-3 rounded-xl border border-border bg-card p-4 shadow-sm"
                        >
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <Target class="h-4 w-4" />
                            </span>
                            <p class="text-sm leading-relaxed text-foreground">{{ item }}</p>
                        </li>
                    </ul>
                </div>
            </template>
        </div>
    </PublicLayout>
</template>
