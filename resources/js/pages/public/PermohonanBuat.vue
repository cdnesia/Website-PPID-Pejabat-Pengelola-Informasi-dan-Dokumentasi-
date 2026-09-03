<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/lib/axios';
import PublicLayout from '@/layouts/PublicLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { toast } from '@/lib/toast';

const router = useRouter();

const form = reactive({
    applicant_name: '',
    applicant_nik: '',
    applicant_occupation: '',
    applicant_phone: '',
    applicant_email: '',
    applicant_address: '',
    information_detail: '',
    purpose: '',
    format_requested: 'digital',
    delivery_method: 'email',
    response_delivery_method: 'email',
});
const ktpFile = ref(null);
const powerOfAttorneyFile = ref(null);
const errors = ref({});
const submitting = ref(false);
const submitted = ref(null);

const deliveryMethods = [
    { value: 'email', label: 'Email' },
    { value: 'datang_langsung', label: 'Datang Langsung' },
    { value: 'pos', label: 'Pos' },
    { value: 'whatsapp', label: 'WhatsApp' },
];

const responseDeliveryMethods = [
    { value: 'email', label: 'Email' },
    { value: 'pos', label: 'Pos' },
    { value: 'diambil_langsung', label: 'Diambil Langsung' },
];

async function submit() {
    submitting.value = true;
    errors.value = {};

    const payload = new FormData();
    Object.entries(form).forEach(([key, value]) => payload.append(key, value));
    if (ktpFile.value) payload.append('ktp', ktpFile.value);
    if (powerOfAttorneyFile.value) payload.append('power_of_attorney', powerOfAttorneyFile.value);

    try {
        const { data } = await api.post('/requests', payload, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        submitted.value = data.data;
        toast({
            title: 'Permohonan berhasil diajukan',
            description: `Nomor permohonan Anda: ${data.data.request_number}`,
            variant: 'success',
        });
    } catch (error) {
        handleSubmitError(error);
    } finally {
        submitting.value = false;
    }
}

function handleSubmitError(error) {
    if (error.response?.status === 422) {
        errors.value = error.response.data.errors ?? {};
        const messages = Object.values(errors.value).flat();
        toast({
            title: 'Data belum lengkap atau tidak valid',
            description: messages[0] ?? 'Periksa kembali isian formulir.',
            variant: 'destructive',
        });
        return;
    }

    if (!error.response) {
        toast({
            title: 'Tidak dapat terhubung ke server',
            description: 'Periksa koneksi internet Anda, lalu coba kirim kembali.',
            variant: 'destructive',
        });
        return;
    }

    if (error.response.status === 429) {
        toast({
            title: 'Terlalu banyak percobaan',
            description: 'Anda telah mencoba beberapa kali dalam waktu singkat. Coba lagi dalam beberapa saat.',
            variant: 'destructive',
        });
        return;
    }

    toast({
        title: 'Gagal mengirim permohonan',
        description: error.response.data?.message ?? `Terjadi kesalahan pada server (${error.response.status}). Coba lagi beberapa saat.`,
        variant: 'destructive',
    });
}
</script>

<template>
    <PublicLayout>
        <PageHeader
            title="Permohonan Informasi"
            subtitle="Lengkapi formulir berikut untuk mengajukan permohonan informasi publik. Anda akan menerima nomor registrasi untuk melacak status permohonan."
        />

        <div class="mx-auto max-w-3xl px-4 py-12">
            <Card v-if="submitted" v-reveal class="border-accent">
                <CardHeader>
                    <CardTitle>Permohonan Berhasil Diajukan</CardTitle>
                    <CardDescription>Simpan nomor permohonan Anda untuk melacak status.</CardDescription>
                </CardHeader>
                <CardContent class="space-y-3">
                    <p class="font-mono text-lg font-semibold text-primary">{{ submitted.request_number }}</p>
                    <Button @click="router.push(`/permohonan/lacak/${submitted.request_number}`)">
                        Lacak Permohonan Ini
                    </Button>
                </CardContent>
            </Card>

            <form v-else class="space-y-8" @submit.prevent="submit">
                <section v-reveal class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-foreground">Data Pemohon</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <Label for="applicant_name">Nama Lengkap</Label>
                            <Input id="applicant_name" v-model="form.applicant_name" placeholder="Contoh: Ahmad Fauzi" class="mt-1" />
                            <p v-if="errors.applicant_name" class="mt-1 text-sm text-destructive">{{ errors.applicant_name[0] }}</p>
                        </div>

                        <div>
                            <Label for="applicant_nik">NIK</Label>
                            <Input id="applicant_nik" v-model="form.applicant_nik" placeholder="16 digit sesuai KTP" class="mt-1" />
                            <p v-if="errors.applicant_nik" class="mt-1 text-sm text-destructive">{{ errors.applicant_nik[0] }}</p>
                        </div>

                        <div>
                            <Label for="applicant_occupation">Pekerjaan</Label>
                            <Input id="applicant_occupation" v-model="form.applicant_occupation" placeholder="Contoh: Mahasiswa, Wiraswasta" class="mt-1" />
                            <p v-if="errors.applicant_occupation" class="mt-1 text-sm text-destructive">{{ errors.applicant_occupation[0] }}</p>
                        </div>

                        <div>
                            <Label for="applicant_phone">No. HP/WhatsApp</Label>
                            <Input id="applicant_phone" v-model="form.applicant_phone" placeholder="08xxxxxxxxxx" class="mt-1" />
                            <p v-if="errors.applicant_phone" class="mt-1 text-sm text-destructive">{{ errors.applicant_phone[0] }}</p>
                        </div>

                        <div>
                            <Label for="applicant_email">Email</Label>
                            <Input id="applicant_email" v-model="form.applicant_email" type="email" placeholder="nama@email.com" class="mt-1" />
                            <p v-if="errors.applicant_email" class="mt-1 text-sm text-destructive">{{ errors.applicant_email[0] }}</p>
                        </div>

                        <div class="sm:col-span-2">
                            <Label for="applicant_address">Alamat</Label>
                            <Textarea
                                id="applicant_address"
                                v-model="form.applicant_address"
                                placeholder="Alamat lengkap sesuai KTP (jalan, RT/RW, kelurahan, kecamatan, kota)"
                                class="mt-1"
                            />
                            <p v-if="errors.applicant_address" class="mt-1 text-sm text-destructive">{{ errors.applicant_address[0] }}</p>
                        </div>
                    </div>
                </section>

                <section v-reveal="80" class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-foreground">Rincian Permohonan</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <Label for="detail">Informasi yang Dibutuhkan</Label>
                            <Textarea
                                id="detail"
                                v-model="form.information_detail"
                                placeholder="Jelaskan secara rinci informasi publik yang Anda butuhkan"
                                class="mt-1"
                            />
                            <p v-if="errors.information_detail" class="mt-1 text-sm text-destructive">{{ errors.information_detail[0] }}</p>
                        </div>

                        <div>
                            <Label for="purpose">Tujuan Penggunaan Informasi</Label>
                            <Textarea
                                id="purpose"
                                v-model="form.purpose"
                                placeholder="Contoh: Penelitian akademik, transparansi publik, dsb."
                                class="mt-1"
                            />
                            <p v-if="errors.purpose" class="mt-1 text-sm text-destructive">{{ errors.purpose[0] }}</p>
                        </div>

                        <div>
                            <Label for="format">Format yang Diinginkan</Label>
                            <select
                                id="format"
                                v-model="form.format_requested"
                                class="mt-1 flex h-10 w-full rounded-md border border-input bg-background px-3 text-sm"
                            >
                                <option value="digital">Digital</option>
                                <option value="cetak">Cetak</option>
                            </select>
                        </div>

                        <div>
                            <Label>Cara Memperoleh Informasi</Label>
                            <div class="mt-2 grid gap-2 sm:grid-cols-2">
                                <label
                                    v-for="option in deliveryMethods"
                                    :key="option.value"
                                    class="flex items-center gap-2 rounded-md border border-input px-3 py-2 text-sm has-[:checked]:border-primary has-[:checked]:bg-primary/5"
                                >
                                    <input v-model="form.delivery_method" type="radio" name="delivery_method" :value="option.value" class="accent-primary">
                                    {{ option.label }}
                                </label>
                            </div>
                            <p v-if="errors.delivery_method" class="mt-1 text-sm text-destructive">{{ errors.delivery_method[0] }}</p>
                        </div>

                        <div>
                            <Label>Cara Pengiriman Jawaban</Label>
                            <div class="mt-2 grid gap-2 sm:grid-cols-3">
                                <label
                                    v-for="option in responseDeliveryMethods"
                                    :key="option.value"
                                    class="flex items-center gap-2 rounded-md border border-input px-3 py-2 text-sm has-[:checked]:border-primary has-[:checked]:bg-primary/5"
                                >
                                    <input v-model="form.response_delivery_method" type="radio" name="response_delivery_method" :value="option.value" class="accent-primary">
                                    {{ option.label }}
                                </label>
                            </div>
                            <p v-if="errors.response_delivery_method" class="mt-1 text-sm text-destructive">{{ errors.response_delivery_method[0] }}</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <Label for="ktp">Upload KTP (opsional)</Label>
                                <input
                                    id="ktp"
                                    type="file"
                                    class="mt-1 block w-full text-sm text-muted-foreground file:mr-3 file:rounded-md file:border-0 file:bg-muted file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-foreground hover:file:bg-muted/70"
                                    @change="ktpFile = $event.target.files[0]"
                                >
                                <p v-if="errors.ktp" class="mt-1 text-sm text-destructive">{{ errors.ktp[0] }}</p>
                            </div>

                            <div>
                                <Label for="poa">Upload Surat Kuasa (jika mewakili)</Label>
                                <input
                                    id="poa"
                                    type="file"
                                    class="mt-1 block w-full text-sm text-muted-foreground file:mr-3 file:rounded-md file:border-0 file:bg-muted file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-foreground hover:file:bg-muted/70"
                                    @change="powerOfAttorneyFile = $event.target.files[0]"
                                >
                                <p v-if="errors.power_of_attorney" class="mt-1 text-sm text-destructive">{{ errors.power_of_attorney[0] }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <Button type="submit" :disabled="submitting" class="w-full">
                    {{ submitting ? 'Mengirim...' : 'Kirim Permohonan' }}
                </Button>
            </form>
        </div>
    </PublicLayout>
</template>
