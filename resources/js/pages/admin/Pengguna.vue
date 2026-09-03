<script setup>
import { onMounted, reactive, ref } from 'vue';
import api from '@/lib/axios';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';

const users = ref([]);
const roles = ref([]);
const loading = ref(true);
const showForm = ref(false);
const editingId = ref(null);
const errors = ref({});
const submitting = ref(false);
const resetMessage = ref('');

const emptyForm = () => ({ name: '', email: '', password: '', phone: '', role: '' });
const form = reactive(emptyForm());

async function loadAll() {
    loading.value = true;
    try {
        const [usersRes, rolesRes] = await Promise.all([
            api.get('/admin/users'),
            api.get('/admin/roles'),
        ]);
        users.value = usersRes.data.data;
        roles.value = rolesRes.data;
    } finally {
        loading.value = false;
    }
}

onMounted(loadAll);

function openCreate() {
    editingId.value = null;
    Object.assign(form, emptyForm());
    errors.value = {};
    showForm.value = true;
}

function openEdit(user) {
    editingId.value = user.id;
    Object.assign(form, {
        name: user.name,
        email: user.email,
        password: '',
        phone: user.phone ?? '',
        role: user.roles?.[0] ?? '',
    });
    errors.value = {};
    showForm.value = true;
}

function closeForm() {
    showForm.value = false;
}

async function submit() {
    submitting.value = true;
    errors.value = {};

    try {
        if (editingId.value) {
            const payload = { ...form };
            if (!payload.password) delete payload.password;
            await api.put(`/admin/users/${editingId.value}`, payload);
        } else {
            await api.post('/admin/users', form);
        }
        showForm.value = false;
        await loadAll();
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        }
    } finally {
        submitting.value = false;
    }
}

async function remove(user) {
    if (!confirm(`Hapus pengguna "${user.name}"?`)) return;
    await api.delete(`/admin/users/${user.id}`);
    await loadAll();
}

async function resetPassword(user) {
    if (!confirm(`Kirim tautan reset kata sandi ke "${user.name}"?`)) return;
    const { data } = await api.post(`/admin/users/${user.id}/reset-password`);
    resetMessage.value = data.message;
}
</script>

<template>
    <AdminLayout>
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold">Manajemen Pengguna</h1>
            <Button v-if="!showForm" size="sm" @click="openCreate">Tambah Pengguna</Button>
        </div>

        <p v-if="resetMessage" class="mt-4 rounded-md bg-accent/20 p-3 text-sm text-accent-foreground">{{ resetMessage }}</p>

        <Card v-if="showForm" class="mt-6">
            <CardHeader>
                <CardTitle class="text-base">{{ editingId ? 'Edit Pengguna' : 'Tambah Pengguna' }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="space-y-4" @submit.prevent="submit">
                    <div>
                        <Label for="name">Nama</Label>
                        <Input id="name" v-model="form.name" class="mt-1" required />
                        <p v-if="errors.name" class="mt-1 text-sm text-destructive">{{ errors.name[0] }}</p>
                    </div>
                    <div>
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" class="mt-1" required />
                        <p v-if="errors.email" class="mt-1 text-sm text-destructive">{{ errors.email[0] }}</p>
                    </div>
                    <div>
                        <Label for="password">{{ editingId ? 'Kata Sandi Baru (opsional)' : 'Kata Sandi' }}</Label>
                        <Input id="password" v-model="form.password" type="password" class="mt-1" :required="!editingId" />
                        <p v-if="errors.password" class="mt-1 text-sm text-destructive">{{ errors.password[0] }}</p>
                    </div>
                    <div>
                        <Label for="phone">Telepon</Label>
                        <Input id="phone" v-model="form.phone" class="mt-1" />
                    </div>
                    <div>
                        <Label for="role">Peran</Label>
                        <select id="role" v-model="form.role" class="mt-1 flex h-10 w-full rounded-md border border-input bg-background px-3 text-sm" required>
                            <option value="" disabled>Pilih peran</option>
                            <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                        </select>
                        <p v-if="errors.role" class="mt-1 text-sm text-destructive">{{ errors.role[0] }}</p>
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
                <CardTitle class="text-base">Daftar Pengguna</CardTitle>
            </CardHeader>
            <CardContent>
                <p v-if="loading" class="text-sm text-muted-foreground">Memuat data...</p>
                <table v-else class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted-foreground">
                            <th class="pb-2">Nama</th>
                            <th class="pb-2">Email</th>
                            <th class="pb-2">Peran</th>
                            <th class="pb-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id" class="border-b border-border last:border-0">
                            <td class="py-2">{{ user.name }}</td>
                            <td class="py-2">{{ user.email }}</td>
                            <td class="py-2">
                                <Badge v-for="role in user.roles" :key="role" variant="outline" class="mr-1">{{ role }}</Badge>
                            </td>
                            <td class="py-2 text-right">
                                <Button size="sm" variant="ghost" @click="openEdit(user)">Edit</Button>
                                <Button size="sm" variant="ghost" @click="resetPassword(user)">Reset Sandi</Button>
                                <Button size="sm" variant="ghost" class="text-destructive" @click="remove(user)">Hapus</Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </AdminLayout>
</template>
