<script setup>
import { onMounted, reactive, ref } from 'vue';
import api from '@/lib/axios';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import RichTextEditor from '@/components/ui/editor/RichTextEditor.vue';
import { ImageOff } from '@lucide/vue';
import { toast } from '@/lib/toast';

const news = ref([]);
const loading = ref(true);
const showForm = ref(false);
const editingSlug = ref(null);
const thumbnail = ref(null);
const thumbnailPreview = ref(null);
const currentThumbnail = ref(null);
const errors = ref({});
const submitting = ref(false);

const emptyForm = () => ({
    title: '',
    excerpt: '',
    content: '',
    category: '',
    is_published: false,
});
const form = reactive(emptyForm());

async function loadNews() {
    loading.value = true;
    try {
        const { data } = await api.get('/admin/news');
        news.value = data.data;
    } finally {
        loading.value = false;
    }
}

onMounted(loadNews);

function openCreate() {
    editingSlug.value = null;
    Object.assign(form, emptyForm());
    thumbnail.value = null;
    thumbnailPreview.value = null;
    currentThumbnail.value = null;
    errors.value = {};
    showForm.value = true;
}

async function openEdit(item) {
    editingSlug.value = item.slug;
    errors.value = {};
    const { data } = await api.get(`/admin/news/${item.slug}`);
    Object.assign(form, {
        title: data.data.title,
        excerpt: data.data.excerpt,
        content: data.data.content ?? '',
        category: data.data.category,
        is_published: !!data.data.published_at,
    });
    thumbnail.value = null;
    thumbnailPreview.value = null;
    currentThumbnail.value = data.data.thumbnail ?? null;
    showForm.value = true;
}

function closeForm() {
    showForm.value = false;
}

function onThumbnailChange(event) {
    const file = event.target.files[0] ?? null;
    thumbnail.value = file;
    thumbnailPreview.value = file ? URL.createObjectURL(file) : null;
}

async function submit() {
    submitting.value = true;
    errors.value = {};

    const payload = new FormData();
    payload.append('title', form.title);
    payload.append('excerpt', form.excerpt ?? '');
    payload.append('content', form.content);
    payload.append('category', form.category ?? '');
    payload.append('is_published', form.is_published ? '1' : '0');
    if (thumbnail.value) payload.append('thumbnail', thumbnail.value);
    if (editingSlug.value) payload.append('_method', 'PUT');

    try {
        if (editingSlug.value) {
            await api.post(`/admin/news/${editingSlug.value}`, payload, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
        } else {
            await api.post('/admin/news', payload, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
        }
        showForm.value = false;
        toast({
            title: 'Berhasil disimpan',
            description: `Berita "${form.title}" berhasil ${editingSlug.value ? 'diperbarui' : 'ditambahkan'}.`,
            variant: 'success',
        });
        await loadNews();
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
        toast({
            title: 'Sesi Anda telah berakhir',
            description: 'Silakan masuk kembali untuk melanjutkan.',
            variant: 'destructive',
        });
        return;
    }

    if (error.response.status === 403) {
        toast({
            title: 'Akses ditolak',
            description: 'Anda tidak memiliki izin untuk melakukan aksi ini.',
            variant: 'destructive',
        });
        return;
    }

    toast({
        title: 'Gagal menyimpan berita',
        description: error.response.data?.message ?? `Terjadi kesalahan pada server (${error.response.status}). Coba lagi beberapa saat.`,
        variant: 'destructive',
    });
}

async function remove(item) {
    if (!confirm(`Hapus berita "${item.title}"?`)) return;
    try {
        await api.delete(`/admin/news/${item.slug}`);
        toast({ title: 'Berita dihapus', description: `"${item.title}" berhasil dihapus.`, variant: 'success' });
        await loadNews();
    } catch (error) {
        toast({
            title: 'Gagal menghapus berita',
            description: error.response?.data?.message ?? 'Terjadi kesalahan pada server. Coba lagi beberapa saat.',
            variant: 'destructive',
        });
    }
}
</script>

<template>
    <AdminLayout>
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold">Manajemen Berita</h1>
            <Button v-if="!showForm" size="sm" @click="openCreate">Tambah Berita</Button>
        </div>

        <Card v-if="showForm" class="mt-6">
            <CardHeader>
                <CardTitle class="text-base">{{ editingSlug ? 'Edit Berita' : 'Tambah Berita' }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="space-y-4" @submit.prevent="submit">
                    <div>
                        <Label for="title">Judul</Label>
                        <Input id="title" v-model="form.title" class="mt-1" required />
                        <p v-if="errors.title" class="mt-1 text-sm text-destructive">{{ errors.title[0] }}</p>
                    </div>
                    <div>
                        <Label for="excerpt">Ringkasan</Label>
                        <Textarea id="excerpt" v-model="form.excerpt" class="mt-1" />
                    </div>
                    <div>
                        <Label for="content">Isi Berita</Label>
                        <RichTextEditor id="content" v-model="form.content" class="mt-1" />
                        <p v-if="errors.content" class="mt-1 text-sm text-destructive">{{ errors.content[0] }}</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <Label for="category">Kategori</Label>
                            <Input id="category" v-model="form.category" class="mt-1" placeholder="Pengumuman / Kegiatan" />
                        </div>
                        <div>
                            <Label for="thumbnail">Gambar Sampul</Label>
                            <div class="mt-1 flex items-center gap-3">
                                <div class="flex h-16 w-24 shrink-0 items-center justify-center overflow-hidden rounded-md border border-border bg-muted">
                                    <img
                                        v-if="thumbnailPreview || currentThumbnail"
                                        :src="thumbnailPreview || currentThumbnail"
                                        alt="Pratinjau gambar sampul"
                                        class="h-full w-full object-cover"
                                    >
                                    <ImageOff v-else class="h-5 w-5 text-muted-foreground" />
                                </div>
                                <input
                                    id="thumbnail"
                                    type="file"
                                    accept="image/*"
                                    class="block w-full text-sm text-muted-foreground file:mr-3 file:rounded-md file:border-0 file:bg-muted file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-foreground hover:file:bg-muted/70"
                                    @change="onThumbnailChange"
                                >
                            </div>
                            <p v-if="errors.thumbnail" class="mt-1 text-sm text-destructive">{{ errors.thumbnail[0] }}</p>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="form.is_published" type="checkbox">
                        Publikasikan sekarang
                    </label>

                    <div class="flex gap-2">
                        <Button type="submit" :disabled="submitting">{{ submitting ? 'Menyimpan...' : 'Simpan' }}</Button>
                        <Button type="button" variant="outline" @click="closeForm">Batal</Button>
                    </div>
                </form>
            </CardContent>
        </Card>

        <Card class="mt-6">
            <CardHeader>
                <CardTitle class="text-base">Daftar Berita</CardTitle>
            </CardHeader>
            <CardContent>
                <p v-if="loading" class="text-sm text-muted-foreground">Memuat data...</p>
                <table v-else class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted-foreground">
                            <th class="pb-2">Gambar</th>
                            <th class="pb-2">Judul</th>
                            <th class="pb-2">Kategori</th>
                            <th class="pb-2">Status</th>
                            <th class="pb-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in news" :key="item.id" class="border-b border-border last:border-0">
                            <td class="py-2">
                                <div class="flex h-10 w-14 items-center justify-center overflow-hidden rounded-md border border-border bg-muted">
                                    <img v-if="item.thumbnail" :src="item.thumbnail" :alt="item.title" class="h-full w-full object-cover">
                                    <ImageOff v-else class="h-4 w-4 text-muted-foreground" />
                                </div>
                            </td>
                            <td class="py-2">{{ item.title }}</td>
                            <td class="py-2">{{ item.category }}</td>
                            <td class="py-2">
                                <Badge :variant="item.published_at ? 'accent' : 'outline'">
                                    {{ item.published_at ? 'Published' : 'Draft' }}
                                </Badge>
                            </td>
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
