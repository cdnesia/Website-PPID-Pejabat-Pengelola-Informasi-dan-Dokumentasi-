<script setup>
import { computed, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import { useSettingsStore } from '@/stores/settings';
import defaultLogoUrl from '@/assets/logo-ppid-umjambi.png';

const settingsStore = useSettingsStore();

onMounted(() => settingsStore.fetchSettings());

const logoUrl = computed(() => settingsStore.settings?.logo_url || defaultLogoUrl);
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-muted px-4 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-6 flex flex-col items-center gap-2 text-center">
                <RouterLink to="/" class="flex items-center">
                    <img :src="logoUrl" alt="PPID Universitas Muhammadiyah Jambi" class="h-12 w-auto">
                </RouterLink>
            </div>
            <slot />
        </div>
    </div>
</template>
