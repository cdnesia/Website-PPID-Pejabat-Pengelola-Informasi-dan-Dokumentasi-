<script setup>
import { onMounted, reactive, ref } from 'vue';
import api from '@/lib/axios';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

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
const settingsSaved = ref(false);
const settingsErrors = ref({});

const workUnits = ref([]);
const workUnitForm = reactive({ id: null, code: '', name: '', head_name: '', head_title: '', is_active: true });
const workUnitErrors = ref({});

const categories = ref([]);
const categoryForm = reactive({ id: null, name: '', type: 'berkala', is_active: true });
const categoryErrors = ref({});

async function loadSettings() {
    const { data } = await api.get('/admin/settings');
    Object.assign(settingsForm, data.data);
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
    settingsSaved.value = false;
    settingsErrors.value = {};

    try {
        await api.put('/admin/settings', settingsForm);
        settingsSaved.value = true;
    } catch (error) {
        if (error.response?.status === 422) {
            settingsErrors.value = error.response.data.errors;
        }
    } finally {
        settingsSubmitting.value = false;
    }
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
    try {
        if (workUnitForm.id) {
            await api.put(`/admin/work-units/${workUnitForm.id}`, workUnitForm);
        } else {
            await api.post('/admin/work-units', workUnitForm);
        }
        resetWorkUnitForm();
        await loadWorkUnits();
    } catch (error) {
        if (error.response?.status === 422) {
            workUnitErrors.value = error.response.data.errors;
        }
    }
}

async function removeWorkUnit(unit) {
    if (!confirm(`Hapus unit kerja "${unit.name}"?`)) return;
    await api.delete(`/admin/work-units/${unit.id}`);
    await loadWorkUnits();
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
    try {
        if (categoryForm.id) {
            await api.put(`/admin/categories/${categoryForm.id}`, categoryForm);
        } else {
            await api.post('/admin/categories', categoryForm);
        }
        resetCategoryForm();
        await loadCategories();
    } catch (error) {
        if (error.response?.status === 422) {
            categoryErrors.value = error.response.data.errors;
        }
    }
}

async function removeCategory(category) {
    if (!confirm(`Hapus kategori "${category.name}"?`)) return;
    await api.delete(`/admin/categories/${category.id}`);
    await loadCategories();
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

                    <p v-if="settingsSaved" class="text-sm text-accent">Pengaturan tersimpan.</p>
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
