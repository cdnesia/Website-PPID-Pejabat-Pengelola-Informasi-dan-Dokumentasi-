<script setup>
import { onMounted, reactive, ref } from 'vue';
import api from '@/lib/axios';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

const objections = ref([]);
const loading = ref(true);
const respondingId = ref(null);
const errors = ref({});
const submitting = ref(false);

const form = reactive({ status: 'in_review', response_text: '' });

const statusLabels = {
    submitted: 'Diajukan',
    in_review: 'Ditinjau',
    answered: 'Dijawab',
    escalated: 'Dieskalasi',
};

async function loadObjections() {
    loading.value = true;
    try {
        const { data } = await api.get('/admin/objections');
        objections.value = data.data;
    } finally {
        loading.value = false;
    }
}

onMounted(loadObjections);

function openRespond(item) {
    respondingId.value = item.id;
    errors.value = {};
    form.status = item.status === 'submitted' ? 'in_review' : item.status;
    form.response_text = item.response_text ?? '';
}

function closeRespond() {
    respondingId.value = null;
}

async function submit(item) {
    submitting.value = true;
    errors.value = {};

    try {
        await api.post(`/admin/objections/${item.id}/respond`, form);
        respondingId.value = null;
        await loadObjections();
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        }
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <AdminLayout>
        <h1 class="text-xl font-bold">Manajemen Keberatan</h1>
        <p class="mt-1 text-sm text-muted-foreground">
            Tinjau dan tanggapi keberatan yang diajukan pemohon atas permohonan informasi.
        </p>

        <Card class="mt-6">
            <CardHeader>
                <CardTitle class="text-base">Daftar Keberatan</CardTitle>
            </CardHeader>
            <CardContent>
                <p v-if="loading" class="text-sm text-muted-foreground">Memuat data...</p>
                <p v-else-if="objections.length === 0" class="text-sm text-muted-foreground">Belum ada keberatan.</p>

                <div v-else class="space-y-4">
                    <div v-for="item in objections" :key="item.id" class="rounded-lg border border-border p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-mono text-sm font-medium">{{ item.request_number }}</p>
                                <p class="text-sm text-muted-foreground">Oleh {{ item.user }}</p>
                            </div>
                            <Badge variant="outline">{{ statusLabels[item.status] ?? item.status }}</Badge>
                        </div>

                        <p class="mt-3 text-sm">{{ item.reason }}</p>

                        <div v-if="item.evidence?.length" class="mt-2">
                            <a
                                v-for="file in item.evidence"
                                :key="file.id"
                                :href="file.url"
                                target="_blank"
                                class="text-sm text-primary hover:underline"
                            >
                                Lihat bukti pendukung
                            </a>
                        </div>

                        <div v-if="item.response_text" class="mt-3 rounded-md bg-muted p-3 text-sm">
                            <p class="mb-1 font-medium">Tanggapan PPID</p>
                            {{ item.response_text }}
                        </div>

                        <template v-if="respondingId === item.id">
                            <form class="mt-4 space-y-3 border-t border-border pt-4" @submit.prevent="submit(item)">
                                <div>
                                    <Label :for="`status-${item.id}`">Status</Label>
                                    <select :id="`status-${item.id}`" v-model="form.status" class="mt-1 flex h-10 w-full rounded-md border border-input bg-background px-3 text-sm">
                                        <option value="in_review">Ditinjau</option>
                                        <option value="answered">Dijawab</option>
                                        <option value="escalated">Dieskalasi</option>
                                    </select>
                                </div>
                                <div>
                                    <Label :for="`response-${item.id}`">Tanggapan</Label>
                                    <Textarea :id="`response-${item.id}`" v-model="form.response_text" class="mt-1" />
                                    <p v-if="errors.response_text" class="mt-1 text-sm text-destructive">{{ errors.response_text[0] }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <Button type="submit" size="sm" :disabled="submitting">{{ submitting ? 'Mengirim...' : 'Kirim Tanggapan' }}</Button>
                                    <Button type="button" size="sm" variant="outline" @click="closeRespond">Batal</Button>
                                </div>
                            </form>
                        </template>
                        <Button v-else size="sm" variant="outline" class="mt-3" @click="openRespond(item)">Tanggapi</Button>
                    </div>
                </div>
            </CardContent>
        </Card>
    </AdminLayout>
</template>
