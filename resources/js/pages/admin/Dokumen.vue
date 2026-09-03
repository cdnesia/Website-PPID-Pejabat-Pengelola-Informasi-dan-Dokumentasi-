<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import api from '@/lib/axios';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { toast } from '@/lib/toast';

const informations = ref([]);
const categories = ref([]);
const workUnits = ref([]);
const loading = ref(true);
const showForm = ref(false);
const editingSlug = ref(null);
const errors = ref({});
const submitting = ref(false);

const emptyForm = () => ({
    category_id: '',
    work_unit_id: '',
    title: '',
    description: '',
    content: '',
    file_url: '',
    status: 'draft',
});
const form = reactive(emptyForm());

async function loadAll() {
    loading.value = true;
    try {
        const [infoRes, catRes, unitRes] = await Promise.all([
            api.get('/admin/informations'),
            api.get('/admin/categories'),
            api.get('/admin/work-units'),
        ]);
        informations.value = infoRes.data.data;
        categories.value = catRes.data.data;
        workUnits.value = unitRes.data.data;
    } catch (error) {
        toast({
            title: 'Gagal memuat data',
            description: error.response?.data?.message ?? 'Terjadi kesalahan pada server. Coba muat ulang halaman.',
            variant: 'destructive',
        });
    } finally {
        loading.value = false;
    }
}

onMounted(loadAll);

function openCreate() {
    editingSlug.value = null;
    Object.assign(form, emptyForm());
    errors.value = {};
    showForm.value = true;
}

async function openEdit(item) {
    editingSlug.value = item.slug;
    errors.value = {};
    try {
        const { data } = await api.get(`/admin/informations/${item.slug}`);
        Object.assign(form, {
            category_id: data.data.category?.id ?? '',
            work_unit_id: data.data.work_unit?.id ?? '',
            title: data.data.title,
            description: data.data.description,
            content: data.data.content ?? '',
            file_url: data.data.file_url ?? '',
            status: data.data.status,
        });
        showForm.value = true;
    } catch (error) {
        toast({
            title: 'Gagal memuat data informasi',
            description: error.response?.data?.message ?? 'Terjadi kesalahan pada server. Coba lagi beberapa saat.',
            variant: 'destructive',
        });
    }
}

function closeForm() {
    showForm.value = false;
}

async function submit() {
    submitting.value = true;
    errors.value = {};

    const payload = {
        category_id: form.category_id,
        work_unit_id: form.work_unit_id || null,
        title: form.title,
        description: form.description ?? '',
        content: form.content ?? '',
        file_url: form.file_url || null,
        status: form.status,
    };

    try {
        if (editingSlug.value) {
            await api.put(`/admin/informations/${editingSlug.value}`, payload);
        } else {
            await api.post('/admin/informations', payload);
        }
        showForm.value = false;
        toast({
            title: 'Berhasil disimpan',
            description: `Informasi "${form.title}" berhasil ${editingSlug.value ? 'diperbarui' : 'ditambahkan'}.`,
            variant: 'success',
        });
        await loadAll();
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
            description: 'Periksa koneksi internet Anda, lalu coba simpan kembali.',
            variant: 'destructive',
        });
        return;
    }

    if (error.response.status === 401 || error.response.status === 419) {
        toast({ title: 'Sesi Anda telah berakhir', description: 'Silakan masuk kembali untuk melanjutkan.', variant: 'destructive' });
        return;
    }

    if (error.response.status === 403) {
        toast({ title: 'Akses ditolak', description: 'Anda tidak memiliki izin untuk melakukan aksi ini.', variant: 'destructive' });
        return;
    }

    toast({
        title: 'Gagal menyimpan informasi',
        description: error.response.data?.message ?? `Terjadi kesalahan pada server (${error.response.status}). Coba lagi beberapa saat.`,
        variant: 'destructive',
    });
}

async function remove(item) {
    if (!confirm(`Hapus informasi "${item.title}"?`)) return;
    try {
        await api.delete(`/admin/informations/${item.slug}`);
        toast({ title: 'Informasi dihapus', description: `"${item.title}" berhasil dihapus.`, variant: 'success' });
        await loadAll();
    } catch (error) {
        toast({
            title: 'Gagal menghapus informasi',
            description: error.response?.data?.message ?? 'Terjadi kesalahan pada server. Coba lagi beberapa saat.',
            variant: 'destructive',
        });
    }
}

const statusVariant = computed(() => (status) => ({
    draft: 'outline',
    published: 'accent',
    archived: 'secondary',
})[status] ?? 'outline');
</script>

<template>
    <AdminLayout>
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold">Manajemen Konten &amp; Dokumen</h1>
            <Button v-if="!showForm" size="sm" @click="openCreate">Tambah Informasi</Button>
        </div>

        <Card v-if="showForm" class="mt-6">
            <CardHeader>
                <CardTitle class="text-base">{{ editingSlug ? 'Edit Informasi' : 'Tambah Informasi' }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="space-y-4" @submit.prevent="submit">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <Label for="category_id">Kategori</Label>
                            <select id="category_id" v-model="form.category_id" class="mt-1 flex h-10 w-full rounded-md border border-input bg-background px-3 text-sm" required>
                                <option value="" disabled>Pilih kategori</option>
                                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <p v-if="errors.category_id" class="mt-1 text-sm text-destructive">{{ errors.category_id[0] }}</p>
                        </div>
                        <div>
                            <Label for="work_unit_id">Unit Kerja</Label>
                            <select id="work_unit_id" v-model="form.work_unit_id" class="mt-1 flex h-10 w-full rounded-md border border-input bg-background px-3 text-sm">
                                <option value="">- Tidak ada -</option>
                                <option v-for="u in workUnits" :key="u.id" :value="u.id">{{ u.name }}</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <Label for="title">Judul</Label>
                        <Input id="title" v-model="form.title" class="mt-1" required />
                        <p v-if="errors.title" class="mt-1 text-sm text-destructive">{{ errors.title[0] }}</p>
                    </div>

                    <div>
                        <Label for="description">Deskripsi Singkat</Label>
                        <Textarea id="description" v-model="form.description" class="mt-1" />
                    </div>

                    <div>
                        <Label for="content">Konten</Label>
                        <Textarea id="content" v-model="form.content" class="mt-1" rows="6" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <Label for="status">Status</Label>
                            <select id="status" v-model="form.status" class="mt-1 flex h-10 w-full rounded-md border border-input bg-background px-3 text-sm">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <div>
                            <Label for="file_url">Tautan Berkas (opsional)</Label>
                            <Input id="file_url" v-model="form.file_url" class="mt-1" type="url" placeholder="https://drive.google.com/..." />
                            <p v-if="errors.file_url" class="mt-1 text-sm text-destructive">{{ errors.file_url[0] }}</p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <Button type="submit" :disabled="submitting">{{ submitting ? 'Menyimpan...' : 'Simpan' }}</Button>
                        <Button type="button" variant="outline" @click="closeForm">Batal</Button>
                    </div>
                </form>
            </CardContent>
        </Card>

        <Card class="mt-6">
            <CardHeader>
                <CardTitle class="text-base">Daftar Informasi Publik</CardTitle>
                <CardDescription>Berkala, serta-merta, dan setiap saat.</CardDescription>
            </CardHeader>
            <CardContent>
                <p v-if="loading" class="text-sm text-muted-foreground">Memuat data...</p>
                <table v-else class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted-foreground">
                            <th class="pb-2">Judul</th>
                            <th class="pb-2">Kategori</th>
                            <th class="pb-2">Status</th>
                            <th class="pb-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in informations" :key="item.id" class="border-b border-border last:border-0">
                            <td class="py-2">{{ item.title }}</td>
                            <td class="py-2">{{ item.category?.name }}</td>
                            <td class="py-2"><Badge :variant="statusVariant(item.status)">{{ item.status }}</Badge></td>
                            <td class="py-2 text-right">
                                <Button size="sm" variant="ghost" @click="openEdit(item)">Edit</Button>
                                <Button size="sm" variant="ghost" class="text-destructive" @click="remove(item)">Hapus</Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </AdminLayout>
</template>
