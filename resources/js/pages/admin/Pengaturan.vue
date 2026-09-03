<script setup>
import { onMounted, reactive, ref } from 'vue';
import api from '@/lib/axios';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { ImageOff } from '@lucide/vue';
import { toast } from '@/lib/toast';

const settingsForm = reactive({
    org_name: '',
    org_email: '',
    org_phone: '',
    org_address: '',
    response_deadline_days: 10,
    banner_text: '',
    banner_is_active: false,
});
const settingsSubmitting = ref(false);
const settingsErrors = ref({});
const logo = ref(null);
const logoPreview = ref(null);
const currentLogo = ref(null);

const workUnits = ref([]);
const workUnitForm = reactive({ id: null, code: '', name: '', head_name: '', head_title: '', is_active: true });
const workUnitErrors = ref({});

const categories = ref([]);
const categoryForm = reactive({ id: null, name: '', type: 'berkala', is_active: true });
const categoryErrors = ref({});

async function loadSettings() {
    const { data } = await api.get('/admin/settings');
    Object.assign(settingsForm, data.data);
    currentLogo.value = data.data.logo_url ?? null;
}

function onLogoChange(event) {
    const file = event.target.files[0] ?? null;
    logo.value = file;
    logoPreview.value = file ? URL.createObjectURL(file) : null;
}

async function loadWorkUnits() {
    const { data } = await api.get('/admin/work-units');
    workUnits.value = data.data;
}

async function loadCategories() {
    const { data } = await api.get('/admin/categories');
    categories.value = data.data;
}

onMounted(() => {
    loadSettings();
    loadWorkUnits();
    loadCategories();
});

async function saveSettings() {
    settingsSubmitting.value = true;
    settingsErrors.value = {};

    const payload = new FormData();
    payload.append('org_name', settingsForm.org_name);
    payload.append('org_email', settingsForm.org_email ?? '');
    payload.append('org_phone', settingsForm.org_phone ?? '');
    payload.append('org_address', settingsForm.org_address ?? '');
    payload.append('response_deadline_days', settingsForm.response_deadline_days);
    payload.append('banner_text', settingsForm.banner_text ?? '');
    payload.append('banner_is_active', settingsForm.banner_is_active ? '1' : '0');
    if (logo.value) payload.append('logo', logo.value);
    payload.append('_method', 'PUT');

    try {
        const { data } = await api.post('/admin/settings', payload, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        logo.value = null;
        logoPreview.value = null;
        currentLogo.value = data.data.logo_url ?? null;
        toast({ title: 'Pengaturan tersimpan', description: 'Perubahan berhasil disimpan.', variant: 'success' });
    } catch (error) {
        handleSubmitError(error, settingsErrors, 'Gagal menyimpan pengaturan');
    } finally {
        settingsSubmitting.value = false;
    }
}

function handleSubmitError(error, errorsRef, fallbackTitle) {
    if (error.response?.status === 422) {
        errorsRef.value = error.response.data.errors ?? {};
        const messages = Object.values(errorsRef.value).flat();
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
        title: fallbackTitle,
        description: error.response.data?.message ?? `Terjadi kesalahan pada server (${error.response.status}). Coba lagi beberapa saat.`,
        variant: 'destructive',
    });
}

function editWorkUnit(unit) {
    Object.assign(workUnitForm, unit);
}

function resetWorkUnitForm() {
    Object.assign(workUnitForm, { id: null, code: '', name: '', head_name: '', head_title: '', is_active: true });
    workUnitErrors.value = {};
}

async function saveWorkUnit() {
    workUnitErrors.value = {};
    const isEditing = !!workUnitForm.id;
    try {
        if (isEditing) {
            await api.put(`/admin/work-units/${workUnitForm.id}`, workUnitForm);
        } else {
            await api.post('/admin/work-units', workUnitForm);
        }
        toast({
            title: 'Unit kerja tersimpan',
            description: `"${workUnitForm.name}" berhasil ${isEditing ? 'diperbarui' : 'ditambahkan'}.`,
            variant: 'success',
        });
        resetWorkUnitForm();
        await loadWorkUnits();
    } catch (error) {
        handleSubmitError(error, workUnitErrors, 'Gagal menyimpan unit kerja');
    }
}

async function removeWorkUnit(unit) {
    if (!confirm(`Hapus unit kerja "${unit.name}"?`)) return;
    try {
        await api.delete(`/admin/work-units/${unit.id}`);
        toast({ title: 'Unit kerja dihapus', description: `"${unit.name}" berhasil dihapus.`, variant: 'success' });
        await loadWorkUnits();
    } catch (error) {
        toast({
            title: 'Gagal menghapus unit kerja',
            description: error.response?.data?.message ?? 'Terjadi kesalahan pada server. Coba lagi beberapa saat.',
            variant: 'destructive',
        });
    }
}

function editCategory(category) {
    Object.assign(categoryForm, category);
}

function resetCategoryForm() {
    Object.assign(categoryForm, { id: null, name: '', type: 'berkala', is_active: true });
    categoryErrors.value = {};
}

async function saveCategory() {
    categoryErrors.value = {};
    const isEditing = !!categoryForm.id;
    try {
        if (isEditing) {
            await api.put(`/admin/categories/${categoryForm.id}`, categoryForm);
        } else {
            await api.post('/admin/categories', categoryForm);
        }
        toast({
            title: 'Kategori tersimpan',
            description: `"${categoryForm.name}" berhasil ${isEditing ? 'diperbarui' : 'ditambahkan'}.`,
            variant: 'success',
        });
        resetCategoryForm();
        await loadCategories();
    } catch (error) {
        handleSubmitError(error, categoryErrors, 'Gagal menyimpan kategori');
    }
}

async function removeCategory(category) {
    if (!confirm(`Hapus kategori "${category.name}"?`)) return;
    try {
        await api.delete(`/admin/categories/${category.id}`);
        toast({ title: 'Kategori dihapus', description: `"${category.name}" berhasil dihapus.`, variant: 'success' });
        await loadCategories();
    } catch (error) {
        toast({
            title: 'Gagal menghapus kategori',
            description: error.response?.data?.message ?? 'Terjadi kesalahan pada server. Coba lagi beberapa saat.',
            variant: 'destructive',
        });
    }
}
</script>

<template>
    <AdminLayout>
        <h1 class="text-xl font-bold">Pengaturan Sistem</h1>

        <Card class="mt-6">
            <CardHeader>
                <CardTitle class="text-base">Profil Organisasi</CardTitle>
                <CardDescription>Informasi ini tampil pada halaman publik dan template notifikasi.</CardDescription>
            </CardHeader>
            <CardContent>
                <form class="space-y-4" @submit.prevent="saveSettings">
                    <div>
                        <Label for="logo">Logo</Label>
                        <div class="mt-1 flex items-center gap-3">
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-md border border-border bg-muted">
                                <img
                                    v-if="logoPreview || currentLogo"
                                    :src="logoPreview || currentLogo"
                                    alt="Pratinjau logo"
                                    class="h-full w-full object-contain"
                                >
                                <ImageOff v-else class="h-5 w-5 text-muted-foreground" />
                            </div>
                            <input
                                id="logo"
                                type="file"
                                accept="image/*"
                                class="block w-full text-sm text-muted-foreground file:mr-3 file:rounded-md file:border-0 file:bg-muted file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-foreground hover:file:bg-muted/70"
                                @change="onLogoChange"
                            >
                        </div>
                        <p v-if="settingsErrors.logo" class="mt-1 text-sm text-destructive">{{ settingsErrors.logo[0] }}</p>
                    </div>
                    <div>
                        <Label for="org_name">Nama Instansi</Label>
                        <Input id="org_name" v-model="settingsForm.org_name" class="mt-1" required />
                        <p v-if="settingsErrors.org_name" class="mt-1 text-sm text-destructive">{{ settingsErrors.org_name[0] }}</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <Label for="org_email">Email</Label>
                            <Input id="org_email" v-model="settingsForm.org_email" type="email" class="mt-1" />
                        </div>
                        <div>
                            <Label for="org_phone">Telepon</Label>
                            <Input id="org_phone" v-model="settingsForm.org_phone" class="mt-1" />
                        </div>
                    </div>
                    <div>
                        <Label for="org_address">Alamat</Label>
                        <Textarea id="org_address" v-model="settingsForm.org_address" class="mt-1" />
                    </div>
                    <div>
                        <Label for="response_deadline_days">Batas Waktu Respons (hari kerja)</Label>
                        <Input id="response_deadline_days" v-model.number="settingsForm.response_deadline_days" type="number" min="1" class="mt-1 max-w-[150px]" />
                        <p v-if="settingsErrors.response_deadline_days" class="mt-1 text-sm text-destructive">{{ settingsErrors.response_deadline_days[0] }}</p>
                    </div>
                    <div>
                        <Label for="banner_text">Teks Pengumuman/Banner</Label>
                        <Textarea id="banner_text" v-model="settingsForm.banner_text" class="mt-1" />
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="settingsForm.banner_is_active" type="checkbox">
                        Tampilkan banner di halaman publik
                    </label>

                    <Button type="submit" :disabled="settingsSubmitting">{{ settingsSubmitting ? 'Menyimpan...' : 'Simpan Pengaturan' }}</Button>
                </form>
            </CardContent>
        </Card>

        <Card class="mt-6">
            <CardHeader>
                <CardTitle class="text-base">Unit Kerja</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="grid gap-3 sm:grid-cols-5 sm:items-end" @submit.prevent="saveWorkUnit">
                    <div>
                        <Label>Kode</Label>
                        <Input v-model="workUnitForm.code" class="mt-1" required />
                    </div>
                    <div class="sm:col-span-2">
                        <Label>Nama Unit</Label>
                        <Input v-model="workUnitForm.name" class="mt-1" required />
                    </div>
                    <div>
                        <Label>Kepala Unit</Label>
                        <Input v-model="workUnitForm.head_name" class="mt-1" />
                    </div>
                    <div class="flex gap-2">
                        <Button type="submit" size="sm">{{ workUnitForm.id ? 'Update' : 'Tambah' }}</Button>
                        <Button v-if="workUnitForm.id" type="button" size="sm" variant="outline" @click="resetWorkUnitForm">Batal</Button>
                    </div>
                </form>
                <p v-if="workUnitErrors.code" class="mt-1 text-sm text-destructive">{{ workUnitErrors.code[0] }}</p>

                <table class="mt-4 w-full text-sm">
                    <tbody>
                        <tr v-for="unit in workUnits" :key="unit.id" class="border-b border-border last:border-0">
                            <td class="py-2 font-mono">{{ unit.code }}</td>
                            <td class="py-2">{{ unit.name }}</td>
                            <td class="py-2">{{ unit.head_name }}</td>
                            <td class="py-2 text-right">
                                <Button size="sm" variant="ghost" @click="editWorkUnit(unit)">Edit</Button>
                                <Button size="sm" variant="ghost" class="text-destructive" @click="removeWorkUnit(unit)">Hapus</Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>

        <Card class="mt-6">
            <CardHeader>
                <CardTitle class="text-base">Kategori Informasi</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="grid gap-3 sm:grid-cols-4 sm:items-end" @submit.prevent="saveCategory">
                    <div class="sm:col-span-2">
                        <Label>Nama Kategori</Label>
                        <Input v-model="categoryForm.name" class="mt-1" required />
                    </div>
                    <div>
                        <Label>Jenis</Label>
                        <select v-model="categoryForm.type" class="mt-1 flex h-10 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="berkala">Berkala</option>
                            <option value="serta_merta">Serta-Merta</option>
                            <option value="setiap_saat">Setiap Saat</option>
                            <option value="dikecualikan">Dikecualikan</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <Button type="submit" size="sm">{{ categoryForm.id ? 'Update' : 'Tambah' }}</Button>
                        <Button v-if="categoryForm.id" type="button" size="sm" variant="outline" @click="resetCategoryForm">Batal</Button>
                    </div>
                </form>
                <p v-if="categoryErrors.name" class="mt-1 text-sm text-destructive">{{ categoryErrors.name[0] }}</p>

                <table class="mt-4 w-full text-sm">
                    <tbody>
                        <tr v-for="category in categories" :key="category.id" class="border-b border-border last:border-0">
                            <td class="py-2">{{ category.name }}</td>
                            <td class="py-2">{{ category.type }}</td>
                            <td class="py-2 text-right">
                                <Button size="sm" variant="ghost" @click="editCategory(category)">Edit</Button>
                                <Button size="sm" variant="ghost" class="text-destructive" @click="removeCategory(category)">Hapus</Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </AdminLayout>
</template>
