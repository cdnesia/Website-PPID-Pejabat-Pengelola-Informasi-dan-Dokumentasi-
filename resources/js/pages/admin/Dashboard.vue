<script setup>
import { computed, onMounted, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import api from '@/lib/axios';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '@/components/ui/card';

const summary = ref(null);
const monthlyTrend = ref([]);
const recentRequests = ref([]);
const nearingDueDate = ref([]);
const loading = ref(true);

onMounted(async () => {
    try {
        const { data } = await api.get('/admin/dashboard');
        summary.value = data.summary;
        monthlyTrend.value = data.monthly_trend;
        recentRequests.value = data.recent_requests;
        nearingDueDate.value = data.nearing_due_date;
    } finally {
        loading.value = false;
    }
});

const statCards = computed(() => [
    { label: 'Total Permohonan', value: summary.value?.total ?? 0 },
    { label: 'Diajukan', value: summary.value?.submitted ?? 0 },
    { label: 'Diproses', value: summary.value?.in_process ?? 0 },
    { label: 'Dijawab', value: summary.value?.answered ?? 0 },
    { label: 'Ditolak', value: summary.value?.rejected ?? 0 },
]);

const chartOptions = computed(() => ({
    chart: { toolbar: { show: false } },
    xaxis: { categories: monthlyTrend.value.map((item) => item.month) },
    colors: ['#7A1F2D'],
    dataLabels: { enabled: false },
}));

const chartSeries = computed(() => [
    { name: 'Permohonan', data: monthlyTrend.value.map((item) => item.total) },
]);
</script>

<template>
    <AdminLayout>
        <h1 class="text-xl font-bold">Dashboard</h1>

        <div v-if="!loading" class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <Card v-for="card in statCards" :key="card.label">
                <CardHeader class="pb-2">
                    <CardDescription>{{ card.label }}</CardDescription>
                    <CardTitle class="text-2xl">{{ card.value }}</CardTitle>
                </CardHeader>
            </Card>
        </div>

        <div class="mt-6 grid gap-4 lg:grid-cols-3">
            <Card class="lg:col-span-2">
                <CardHeader>
                    <CardTitle class="text-base">Tren Permohonan Bulanan</CardTitle>
                </CardHeader>
                <CardContent>
                    <VueApexCharts v-if="!loading" type="bar" height="280" :options="chartOptions" :series="chartSeries" />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Mendekati Batas Waktu</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <p v-if="nearingDueDate.length === 0" class="text-sm text-muted-foreground">Tidak ada.</p>
                    <div v-for="item in nearingDueDate" :key="item.id" class="text-sm">
                        <p class="font-medium">{{ item.request_number }}</p>
                        <p class="text-muted-foreground">Batas: {{ new Date(item.due_date).toLocaleDateString('id-ID') }}</p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <Card class="mt-6">
            <CardHeader>
                <CardTitle class="text-base">Permohonan Terbaru</CardTitle>
            </CardHeader>
            <CardContent>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted-foreground">
                            <th class="pb-2">Nomor</th>
                            <th class="pb-2">Pemohon</th>
                            <th class="pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in recentRequests" :key="item.id" class="border-b border-border last:border-0">
                            <td class="py-2 font-mono">{{ item.request_number }}</td>
                            <td class="py-2">{{ item.user?.name }}</td>
                            <td class="py-2">{{ item.status }}</td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </AdminLayout>
</template>
