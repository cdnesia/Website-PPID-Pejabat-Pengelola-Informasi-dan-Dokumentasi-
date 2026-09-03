<script setup>
import { onMounted, reactive, ref } from 'vue';
import api from '@/lib/axios';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

const requests = ref([]);
const admins = ref([]);
const loading = ref(true);
const selected = ref(null);
const errors = ref({});
const submittingStatus = ref(false);
const submittingResponse = ref(false);

const statusLabels = {
    draft: 'Draf',
    submitted: 'Diajukan',
    in_review: 'Ditinjau',
    in_process: 'Diproses',
    answered: 'Dijawab',
    rejected: 'Ditolak',
};

const statusForm = reactive({ status: '', rejection_reason: '', assigned_to: '' });
const responseForm = reactive({ response_text: '' });
const responseFile = ref(null);

async function loadRequests() {
    loading.value = true;
    try {
        const { data } = await api.get('/admin/requests');
        requests.value = data.data;
    } finally {
        loading.value = false;
    }
}

async function loadAdmins() {
    const { data } = await api.get('/admin/users');
    admins.value = data.data.filter((user) => !user.roles.includes('pemohon'));
}

onMounted(() => {
    loadRequests();
    loadAdmins();
});

async function openDetail(item) {
    const { data } = await api.get(`/admin/requests/${item.request_number}`);
    selected.value = data.data;
    statusForm.status = data.data.status;
    statusForm.rejection_reason = data.data.rejection_reason ?? '';
    statusForm.assigned_to = data.data.assigned_to?.id ?? '';
    responseForm.response_text = '';
    responseFile.value = null;
    errors.value = {};
}

function closeDetail() {
    selected.value = null;
}

async function updateStatus() {
    submittingStatus.value = true;
    errors.value = {};

    try {
        const { data } = await api.put(`/admin/requests/${selected.value.request_number}/status`, statusForm);
        selected.value = data.data;
        await loadRequests();
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        }
    } finally {
        submittingStatus.value = false;
    }
}

async function sendResponse() {
    submittingResponse.value = true;
    errors.value = {};

    const payload = new FormData();
    payload.append('response_text', responseForm.response_text);
    if (responseFile.value) payload.append('file', responseFile.value);

    try {
        const { data } = await api.post(`/admin/requests/${selected.value.request_number}/respond`, payload, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        selected.value = data.data;
        responseForm.response_text = '';
        responseFile.value = null;
        await loadRequests();
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        }
    } finally {
        submittingResponse.value = false;
    }
}
</script>

<template>
    <AdminLayout>
        <h1 class="text-xl font-bold">Manajemen Permohonan</h1>

        <div class="mt-6 grid gap-6" :class="selected ? 'lg:grid-cols-2' : ''">
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Daftar Permohonan</CardTitle>
                </CardHeader>
                <CardContent>
                    <p v-if="loading" class="text-sm text-muted-foreground">Memuat data...</p>
                    <table v-else class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border text-left text-muted-foreground">
                                <th class="pb-2">Nomor</th>
                                <th class="pb-2">Pemohon</th>
                                <th class="pb-2">Status</th>
                                <th class="pb-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in requests" :key="item.id" class="border-b border-border last:border-0">
                                <td class="py-2 font-mono">{{ item.request_number }}</td>
                                <td class="py-2">{{ item.applicant_name ?? item.user?.name }}</td>
                                <td class="py-2"><Badge variant="outline">{{ statusLabels[item.status] ?? item.status }}</Badge></td>
                                <td class="py-2 text-right">
                                    <Button size="sm" variant="ghost" @click="openDetail(item)">Detail</Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>

            <Card v-if="selected">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle class="font-mono text-base">{{ selected.request_number }}</CardTitle>
                        <Button size="sm" variant="ghost" @click="closeDetail">Tutup</Button>
                    </div>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="text-sm">
                        <p>
                            <span class="font-medium">Pemohon:</span>
                            {{ selected.applicant_name ?? selected.user?.name }}
                            ({{ selected.applicant_email ?? selected.user?.email }})
                        </p>
                        <p v-if="selected.applicant_nik" class="mt-1"><span class="font-medium">NIK:</span> {{ selected.applicant_nik }}</p>
                        <p v-if="selected.applicant_occupation" class="mt-1"><span class="font-medium">Pekerjaan:</span> {{ selected.applicant_occupation }}</p>
                        <p v-if="selected.applicant_phone" class="mt-1"><span class="font-medium">No. HP:</span> {{ selected.applicant_phone }}</p>
                        <p v-if="selected.applicant_address" class="mt-1"><span class="font-medium">Alamat:</span> {{ selected.applicant_address }}</p>
                        <p class="mt-1"><span class="font-medium">Tujuan:</span> {{ selected.purpose }}</p>
                        <p class="mt-1"><span class="font-medium">Rincian:</span> {{ selected.information_detail }}</p>
                        <p class="mt-1"><span class="font-medium">Format:</span> {{ selected.format_requested }}</p>
                        <p v-if="selected.delivery_method" class="mt-1"><span class="font-medium">Cara Memperoleh Informasi:</span> {{ selected.delivery_method }}</p>
                        <p v-if="selected.response_delivery_method" class="mt-1"><span class="font-medium">Cara Pengiriman Jawaban:</span> {{ selected.response_delivery_method }}</p>
                    </div>

                    <form class="space-y-3 border-t border-border pt-4" @submit.prevent="updateStatus">
                        <p class="text-sm font-medium">Ubah Status &amp; Penugasan</p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <Label for="status">Status</Label>
                                <select id="status" v-model="statusForm.status" class="mt-1 flex h-10 w-full rounded-md border border-input bg-background px-3 text-sm">
                                    <option v-for="(label, value) in statusLabels" :key="value" :value="value">{{ label }}</option>
                                </select>
                            </div>
                            <div>
                                <Label for="assigned_to">Ditugaskan ke</Label>
                                <select id="assigned_to" v-model="statusForm.assigned_to" class="mt-1 flex h-10 w-full rounded-md border border-input bg-background px-3 text-sm">
                                    <option value="">- Belum ditugaskan -</option>
                                    <option v-for="admin in admins" :key="admin.id" :value="admin.id">{{ admin.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div v-if="statusForm.status === 'rejected'">
                            <Label for="rejection_reason">Alasan Penolakan</Label>
                            <Textarea id="rejection_reason" v-model="statusForm.rejection_reason" class="mt-1" />
                            <p v-if="errors.rejection_reason" class="mt-1 text-sm text-destructive">{{ errors.rejection_reason[0] }}</p>
                        </div>
                        <Button type="submit" size="sm" :disabled="submittingStatus">
                            {{ submittingStatus ? 'Menyimpan...' : 'Simpan Status' }}
                        </Button>
                    </form>

                    <form class="space-y-3 border-t border-border pt-4" @submit.prevent="sendResponse">
                        <p class="text-sm font-medium">Kirim Jawaban</p>
                        <Textarea v-model="responseForm.response_text" placeholder="Tulis jawaban untuk pemohon..." required />
                        <p v-if="errors.response_text" class="text-sm text-destructive">{{ errors.response_text[0] }}</p>
                        <input type="file" class="block w-full text-sm" @change="responseFile = $event.target.files[0]">
                        <Button type="submit" size="sm" :disabled="submittingResponse">
                            {{ submittingResponse ? 'Mengirim...' : 'Kirim Jawaban' }}
                        </Button>
                    </form>

                    <div v-if="selected.responses?.length" class="border-t border-border pt-4">
                        <p class="mb-2 text-sm font-medium">Riwayat Jawaban</p>
                        <div v-for="response in selected.responses" :key="response.id" class="mb-2 rounded-md bg-muted p-3 text-sm">
                            {{ response.response_text }}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
