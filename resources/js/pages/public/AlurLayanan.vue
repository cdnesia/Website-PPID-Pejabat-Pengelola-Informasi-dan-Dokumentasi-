<script setup>
import PublicLayout from '@/layouts/PublicLayout.vue';

const steps = [
    'Pemohon registrasi/login',
    'Isi form permohonan',
    'Upload KTP (opsional)',
    'Kirim permohonan',
    'Notifikasi ke PPID',
    'PPID validasi (1x24 jam)',
    'PPID proses (maks. 10 hari kerja)',
    'Kirim jawaban',
    'Pemohon terima jawaban',
];
</script>

<template>
    <PublicLayout>
        <div class="mx-auto max-w-3xl px-4 py-10">
            <h1 class="text-2xl font-bold">Alur Layanan Permohonan Informasi</h1>
            <ol class="mt-6 space-y-3">
                <li v-for="(step, index) in steps" :key="step" class="flex items-center gap-3 text-sm">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-semibold text-primary-foreground">
                        {{ index + 1 }}
                    </span>
                    {{ step }}
                </li>
            </ol>
        </div>
    </PublicLayout>
</template>
