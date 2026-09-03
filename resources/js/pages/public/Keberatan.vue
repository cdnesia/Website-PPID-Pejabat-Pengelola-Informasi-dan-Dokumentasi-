<script setup>
import { reactive, ref } from 'vue';
import api from '@/lib/axios';
import { useAuthStore } from '@/stores/auth';
import PublicLayout from '@/layouts/PublicLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';

const auth = useAuthStore();

const form = reactive({
    request_number: '',
    reason: '',
});
const evidenceFile = ref(null);
const errors = ref({});
const submitting = ref(false);
const success = ref(false);
const resendMessage = ref('');
const resending = ref(false);

async function resendVerification() {
    resending.value = true;
    resendMessage.value = '';
    try {
        const { data } = await api.post('/auth/email/verification-notification');
        resendMessage.value = data.message;
    } finally {
        resending.value = false;
    }
}

async function submit() {
    submitting.value = true;
    errors.value = {};
    success.value = false;

    const payload = new FormData();
    payload.append('reason', form.reason);
    if (evidenceFile.value) payload.append('evidence', evidenceFile.value);

    try {
        await api.post(`/requests/${form.request_number}/objection`, payload, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        success.value = true;
        form.reason = '';
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        } else if (error.response?.status === 404) {
            errors.value = { request_number: ['Nomor permohonan tidak ditemukan.'] };
        } else if (error.response?.status === 403) {
            errors.value = { request_number: ['Permohonan ini bukan milik akun Anda.'] };
        }
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <PublicLayout>
        <PageHeader
            title="Ajukan Keberatan"
            subtitle="Ajukan keberatan jika permohonan Anda ditolak tanpa alasan jelas, tidak dijawab dalam batas waktu, atau biaya yang dikenakan tidak wajar."
        />
        <div class="mx-auto max-w-xl px-4 py-12">
            <Card v-if="!auth.user?.email_verified_at" v-reveal class="border-secondary">
                <CardHeader>
                    <CardTitle>Verifikasi Email Diperlukan</CardTitle>
                    <CardDescription>
                        Verifikasi email Anda terlebih dahulu sebelum dapat mengajukan keberatan.
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-2">
                    <p v-if="resendMessage" class="text-sm text-accent">{{ resendMessage }}</p>
                    <Button variant="outline" :disabled="resending" @click="resendVerification">
                        {{ resending ? 'Mengirim...' : 'Kirim Ulang Email Verifikasi' }}
                    </Button>
                </CardContent>
            </Card>

            <Card v-else-if="success" v-reveal class="border-accent">
                <CardHeader>
                    <CardTitle>Keberatan Terkirim</CardTitle>
                    <CardDescription>Tim PPID akan meninjau keberatan Anda.</CardDescription>
                </CardHeader>
            </Card>

            <form v-else v-reveal class="space-y-5" @submit.prevent="submit">
                <div>
                    <Label for="request_number">Nomor Permohonan</Label>
                    <Input id="request_number" v-model="form.request_number" placeholder="PPID-2026-0001" class="mt-1" required />
                    <p v-if="errors.request_number" class="mt-1 text-sm text-destructive">{{ errors.request_number[0] }}</p>
                </div>

                <div>
                    <Label for="reason">Alasan Keberatan</Label>
                    <Textarea id="reason" v-model="form.reason" class="mt-1" required />
                    <p v-if="errors.reason" class="mt-1 text-sm text-destructive">{{ errors.reason[0] }}</p>
                </div>

                <div>
                    <Label for="evidence">Bukti Pendukung (opsional)</Label>
                    <input
                        id="evidence"
                        type="file"
                        class="mt-1 block w-full text-sm"
                        @change="evidenceFile = $event.target.files[0]"
                    >
                </div>

                <Button type="submit" :disabled="submitting" class="w-full">
                    {{ submitting ? 'Mengirim...' : 'Kirim Keberatan' }}
                </Button>
            </form>
        </div>
    </PublicLayout>
</template>
