<script setup>
import { RouterLink } from 'vue-router';
import logoUrl from '@/assets/logo-ppid-umjambi.png';
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
