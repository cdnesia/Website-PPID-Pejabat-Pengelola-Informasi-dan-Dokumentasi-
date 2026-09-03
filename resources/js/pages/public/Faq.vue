<script setup>
import PublicLayout from '@/layouts/PublicLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';

const faqs = [
    {
        q: 'Siapa saja yang dapat mengajukan permohonan informasi?',
        a: 'Setiap warga negara Indonesia dan badan hukum Indonesia dapat mengajukan permohonan informasi publik.',
    },
    {
        q: 'Berapa lama waktu respons permohonan?',
        a: 'PPID wajib memberikan jawaban paling lambat 10 hari kerja sejak permohonan diterima, dan dapat diperpanjang 7 hari kerja.',
    },
    {
        q: 'Apakah permohonan informasi dikenakan biaya?',
        a: 'Permohonan informasi tidak dikenakan biaya, kecuali biaya penggandaan atau pengiriman dokumen yang wajar.',
    },
    {
        q: 'Bagaimana jika permohonan saya ditolak?',
        a: 'Anda dapat mengajukan keberatan melalui menu Keberatan pada situs ini.',
    },
];
</script>

<template>
    <PublicLayout>
        <PageHeader title="Pertanyaan yang Sering Diajukan" subtitle="Jawaban atas pertanyaan umum seputar layanan informasi publik." />
        <div class="mx-auto max-w-3xl px-4 py-12">
            <div class="space-y-4">
                <div
                    v-for="(item, index) in faqs"
                    :key="item.q"
                    v-reveal="index * 100"
                    class="rounded-lg border border-border bg-card p-4 shadow-sm"
                >
                    <p class="font-medium text-foreground">{{ item.q }}</p>
                    <p class="mt-1 text-sm text-muted-foreground">{{ item.a }}</p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
