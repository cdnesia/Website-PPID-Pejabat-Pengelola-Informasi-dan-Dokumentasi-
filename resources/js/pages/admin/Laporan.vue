<script setup>
import { onMounted, ref } from 'vue';
import api from '@/lib/axios';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '@/components/ui/card';

const report = ref(null);
const loading = ref(true);

const statusLabels = {
    draft: 'Draf',
    submitted: 'Diajukan',
    in_review: 'Ditinjau',
    in_process: 'Diproses',
    answered: 'Dijawab',
    rejected: 'Ditolak',
};

onMounted(async () => {
    try {
        const { data } = await api.get('/admin/reports');
        report.value = data;
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <AdminLayout>
        <h1 class="text-xl font-bold">Laporan &amp; Statistik</h1>
        <p v-if="report" class="mt-1 text-sm text-muted-foreground">
            Periode {{ report.period.from }} &mdash; {{ report.period.to }}
        </p>

        <div v-if="!loading" class="mt-6 grid gap-4 lg:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Permohonan per Status</CardTitle>
                    <CardDescription>Ekspor Excel/PDF akan dilengkapi pada iterasi berikutnya.</CardDescription>
                </CardHeader>
                <CardContent>
                    <table class="w-full text-sm">
                        <tbody>
                            <tr v-for="(total, status) in report.by_status" :key="status" class="border-b border-border last:border-0">
                                <td class="py-2">{{ statusLabels[status] ?? status }}</td>
                                <td class="py-2 text-right font-medium">{{ total }}</td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Permohonan per Format</CardTitle>
                </CardHeader>
                <CardContent>
                    <table class="w-full text-sm">
                        <tbody>
                            <tr v-for="(total, format) in report.by_format" :key="format" class="border-b border-border last:border-0">
                                <td class="py-2 capitalize">{{ format }}</td>
                                <td class="py-2 text-right font-medium">{{ total }}</td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
