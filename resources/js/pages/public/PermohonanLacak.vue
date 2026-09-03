<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/lib/axios';
import PublicLayout from '@/layouts/PublicLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import EmptyState from '@/components/shared/EmptyState.vue';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Calendar, FileSearch, MessageSquare, Search } from '@lucide/vue';

const route = useRoute();
const router = useRouter();

const requestNumber = ref(route.params.nomor ?? '');
const result = ref(null);
const notFound = ref(false);
const emptyInputError = ref(false);
const loading = ref(false);

const statusLabels = {
    draft: 'Draf',
    submitted: 'Diajukan',
    in_review: 'Ditinjau',
    in_process: 'Diproses',
    answered: 'Dijawab',
    rejected: 'Ditolak',
};

const statusVariants = {
    draft: 'outline',
    submitted: 'secondary',
    in_review: 'secondary',
    in_process: 'secondary',
    answered: 'accent',
    rejected: 'destructive',
};

const formatLabels = { digital: 'Digital', cetak: 'Cetak' };
const deliveryLabels = { email: 'Email', datang_langsung: 'Datang Langsung', pos: 'Pos', whatsapp: 'WhatsApp' };
const responseDeliveryLabels = { email: 'Email', pos: 'Pos', diambil_langsung: 'Diambil Langsung' };

async function track() {
    const number = requestNumber.value.trim();
    emptyInputError.value = false;

    if (!number) {
        emptyInputError.value = true;
        return;
    }

    loading.value = true;
    notFound.value = false;
    result.value = null;
    router.replace(`/permohonan/lacak/${number}`);

    try {
        const { data } = await api.get(`/requests/track/${number}`);
        result.value = data.data;
    } catch {
        notFound.value = true;
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    if (requestNumber.value) track();
});
</script>

<template>
    <PublicLayout>
        <PageHeader title="Lacak Permohonan" subtitle="Masukkan nomor permohonan Anda untuk memeriksa status terkini." />

        <div class="mx-auto max-w-2xl px-4 py-12">
            <form class="flex gap-2" @submit.prevent="track">
                <Input v-model="requestNumber" placeholder="PPID-2026-0001" class="flex-1" />
                <Button type="submit" :disabled="loading">
                    <Search class="h-4 w-4" />
                    {{ loading ? 'Mencari...' : 'Lacak' }}
                </Button>
            </form>
            <p v-if="emptyInputError" class="mt-1.5 text-sm text-destructive">
                Masukkan nomor permohonan terlebih dahulu, contoh: PPID-2026-0001.
            </p>

            <EmptyState
                v-if="notFound"
                :icon="FileSearch"
                message="Berkas permohonan dengan nomor tersebut tidak ditemukan. Periksa kembali nomor permohonan Anda."
                class="mt-6"
            />

            <Card v-if="result" class="mt-6">
                <CardHeader>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <CardTitle class="font-mono text-base">{{ result.request_number }}</CardTitle>
                        <Badge :variant="statusVariants[result.status] ?? 'outline'">{{ statusLabels[result.status] ?? result.status }}</Badge>
                    </div>
                    <CardDescription class="flex items-center gap-1.5">
                        <Calendar class="h-3.5 w-3.5" />
                        Diajukan {{ new Date(result.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div
                        v-if="result.status === 'rejected' && result.rejection_reason"
                        class="rounded-lg border border-destructive/30 bg-destructive/5 p-3 text-sm text-destructive"
                    >
                        <span class="font-medium">Alasan penolakan:</span> {{ result.rejection_reason }}
                    </div>

                    <dl class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-muted-foreground">Tujuan Penggunaan</dt>
                            <dd class="mt-0.5 font-medium text-foreground">{{ result.purpose }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Format</dt>
                            <dd class="mt-0.5 font-medium text-foreground">{{ formatLabels[result.format_requested] ?? result.format_requested }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-muted-foreground">Rincian Informasi</dt>
                            <dd class="mt-0.5 font-medium text-foreground">{{ result.information_detail }}</dd>
                        </div>
                        <div v-if="result.delivery_method">
                            <dt class="text-muted-foreground">Cara Memperoleh Informasi</dt>
                            <dd class="mt-0.5 font-medium text-foreground">{{ deliveryLabels[result.delivery_method] ?? result.delivery_method }}</dd>
                        </div>
                        <div v-if="result.response_delivery_method">
                            <dt class="text-muted-foreground">Cara Pengiriman Jawaban</dt>
                            <dd class="mt-0.5 font-medium text-foreground">
                                {{ responseDeliveryLabels[result.response_delivery_method] ?? result.response_delivery_method }}
                            </dd>
                        </div>
                        <div v-if="result.due_date">
                            <dt class="text-muted-foreground">Batas Waktu Jawaban</dt>
                            <dd class="mt-0.5 font-medium text-foreground">{{ new Date(result.due_date).toLocaleDateString('id-ID') }}</dd>
                        </div>
                    </dl>

                    <div v-if="result.responses?.length" class="border-t border-border pt-4">
                        <p class="mb-2 flex items-center gap-1.5 text-sm font-semibold text-foreground">
                            <MessageSquare class="h-4 w-4 text-primary" />
                            Jawaban PPID
                        </p>
                        <div v-for="response in result.responses" :key="response.id" class="rounded-lg bg-muted p-3 text-sm text-foreground">
                            {{ response.response_text }}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </PublicLayout>
</template>
