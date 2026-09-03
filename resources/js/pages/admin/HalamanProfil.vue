<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import api from '@/lib/axios';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { toast } from '@/lib/toast';
import { ImageOff, Plus, Trash2 } from '@lucide/vue';

const pages = ref([]);
const loadingList = ref(true);
const activeSlug = ref(null);
const loadingPage = ref(false);
const submitting = ref(false);
const errors = ref({});

const form = reactive({
    title: '',
    subtitle: '',
});

const body = ref('');
const visi = ref('');
const misi = ref([]);
const items = ref([]);

const image = ref(null);
const imagePreview = ref(null);
const currentImage = ref(null);

const kind = computed(() => {
    if (!activeSlug.value) return null;
    if (activeSlug.value === 'tentang-ppid') return 'body';
    if (activeSlug.value === 'visi-misi') return 'visi-misi';
    return 'items';
});

async function loadList() {
    loadingList.value = true;
    try {
        const { data } = await api.get('/admin/pages');
        pages.value = data.data;
        if (pages.value.length) {
            await selectPage(pages.value[0].slug);
        }
    } catch (error) {
        toast({
            title: 'Gagal memuat daftar halaman',
            description: error.response?.data?.message ?? 'Terjadi kesalahan pada server. Coba muat ulang halaman.',
            variant: 'destructive',
        });
    } finally {
        loadingList.value = false;
    }
}

async function selectPage(slug) {
    activeSlug.value = slug;
    errors.value = {};
    loadingPage.value = true;
    try {
        const { data } = await api.get(`/admin/pages/${slug}`);
        form.title = data.data.title;
        form.subtitle = data.data.subtitle ?? '';

        body.value = data.data.content.body ?? '';
        visi.value = data.data.content.visi ?? '';
        misi.value = [...(data.data.content.misi ?? [])];
        items.value = (data.data.content.items ?? []).map((item) => ({ ...item }));

        image.value = null;
        imagePreview.value = null;
        currentImage.value = data.data.image_url ?? null;
    } catch (error) {
        toast({
            title: 'Gagal memuat halaman',
            description: error.response?.data?.message ?? 'Terjadi kesalahan pada server. Coba lagi beberapa saat.',
            variant: 'destructive',
        });
    } finally {
        loadingPage.value = false;
    }
}

function addMisi() {
    misi.value.push('');
}

function removeMisi(index) {
    misi.value.splice(index, 1);
}

function addItem() {
    items.value.push({ title: '', description: '' });
}

function removeItem(index) {
    items.value.splice(index, 1);
}

function onImageChange(event) {
    const file = event.target.files[0] ?? null;
    image.value = file;
    imagePreview.value = file ? URL.createObjectURL(file) : null;
}

function appendContent(payload) {
    if (kind.value === 'body') {
        payload.append('content[body]', body.value);
        return;
    }

    if (kind.value === 'visi-misi') {
        payload.append('content[visi]', visi.value);
        misi.value
            .filter((m) => m.trim() !== '')
            .forEach((m, index) => payload.append(`content[misi][${index}]`, m));
        return;
    }

    items.value
        .filter((item) => item.title.trim() !== '')
        .forEach((item, index) => {
            payload.append(`content[items][${index}][title]`, item.title);
            payload.append(`content[items][${index}][description]`, item.description ?? '');
        });
}

async function submit() {
    submitting.value = true;
    errors.value = {};

    const payload = new FormData();
    payload.append('title', form.title);
    payload.append('subtitle', form.subtitle ?? '');
    appendContent(payload);
    if (image.value) payload.append('image', image.value);
    payload.append('_method', 'PUT');

    try {
        const { data } = await api.post(`/admin/pages/${activeSlug.value}`, payload, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });

        const index = pages.value.findIndex((p) => p.slug === activeSlug.value);
        if (index !== -1) pages.value[index] = data.data;

        image.value = null;
        imagePreview.value = null;
        currentImage.value = data.data.image_url ?? null;

        toast({ title: 'Berhasil disimpan', description: `Halaman "${form.title}" berhasil diperbarui.`, variant: 'success' });
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
        title: 'Gagal menyimpan halaman',
        description: error.response.data?.message ?? `Terjadi kesalahan pada server (${error.response.status}). Coba lagi beberapa saat.`,
        variant: 'destructive',
    });
}

onMounted(loadList);
</script>

<template>
    <AdminLayout>
        <h1 class="text-xl font-bold">Halaman Profil</h1>
        <p class="mt-1 text-sm text-muted-foreground">Kelola konten halaman Tentang PPID, Visi Misi, Struktur Organisasi, Tugas Fungsi, dan Dasar Hukum.</p>

        <p v-if="loadingList" class="mt-6 text-sm text-muted-foreground">Memuat...</p>

        <template v-else>
            <div class="mt-6 flex flex-wrap gap-2">
                <button
                    v-for="p in pages"
                    :key="p.slug"
                    type="button"
                    class="rounded-full px-4 py-1.5 text-sm transition-colors"
                    :class="p.slug === activeSlug ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground hover:bg-muted/70'"
                    @click="selectPage(p.slug)"
                >
                    {{ p.title }}
                </button>
            </div>

            <Card class="mt-4">
                <CardHeader>
                    <CardTitle class="text-base">Edit Konten</CardTitle>
                    <CardDescription>Perubahan akan langsung tampil di halaman publik setelah disimpan.</CardDescription>
                </CardHeader>
                <CardContent>
                    <p v-if="loadingPage" class="text-sm text-muted-foreground">Memuat halaman...</p>

                    <form v-else class="space-y-5" @submit.prevent="submit">
                        <div>
                            <Label for="title">Judul Halaman</Label>
                            <Input id="title" v-model="form.title" class="mt-1" required />
                            <p v-if="errors.title" class="mt-1 text-sm text-destructive">{{ errors.title[0] }}</p>
                        </div>

                        <div>
                            <Label for="subtitle">Subjudul</Label>
                            <Input id="subtitle" v-model="form.subtitle" class="mt-1" />
                            <p v-if="errors.subtitle" class="mt-1 text-sm text-destructive">{{ errors.subtitle[0] }}</p>
                        </div>

                        <div v-if="activeSlug === 'struktur-organisasi'">
                            <Label for="image">Bagan Struktur Organisasi (opsional)</Label>
                            <div class="mt-1 flex items-center gap-3">
                                <div class="flex h-20 w-32 shrink-0 items-center justify-center overflow-hidden rounded-md border border-border bg-muted">
                                    <img
                                        v-if="imagePreview || currentImage"
                                        :src="imagePreview || currentImage"
                                        alt="Pratinjau bagan struktur organisasi"
                                        class="h-full w-full object-cover"
                                    >
                                    <ImageOff v-else class="h-5 w-5 text-muted-foreground" />
                                </div>
                                <input
                                    id="image"
                                    type="file"
                                    accept="image/*"
                                    class="block w-full text-sm text-muted-foreground file:mr-3 file:rounded-md file:border-0 file:bg-muted file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-foreground hover:file:bg-muted/70"
                                    @change="onImageChange"
                                >
                            </div>
                            <p v-if="errors.image" class="mt-1 text-sm text-destructive">{{ errors.image[0] }}</p>
                        </div>

                        <div v-if="kind === 'body'">
                            <Label for="body">Isi Konten</Label>
                            <Textarea id="body" v-model="body" class="mt-1" rows="8" />
                        </div>

                        <template v-else-if="kind === 'visi-misi'">
                            <div>
                                <Label for="visi">Visi</Label>
                                <Textarea id="visi" v-model="visi" class="mt-1" rows="3" />
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <Label>Misi</Label>
                                    <Button type="button" size="sm" variant="outline" @click="addMisi">
                                        <Plus class="h-4 w-4" />
                                        Tambah Poin
                                    </Button>
                                </div>
                                <div class="mt-2 flex flex-col gap-2">
                                    <div v-for="(item, index) in misi" :key="index" class="flex items-center gap-2">
                                        <Input v-model="misi[index]" class="flex-1" />
                                        <Button type="button" size="sm" variant="ghost" class="text-destructive" @click="removeMisi(index)">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                    <p v-if="!misi.length" class="text-sm text-muted-foreground">Belum ada poin misi.</p>
                                </div>
                            </div>
                        </template>

                        <div v-else-if="kind === 'items'">
                            <div class="flex items-center justify-between">
                                <Label>Daftar Item</Label>
                                <Button type="button" size="sm" variant="outline" @click="addItem">
                                    <Plus class="h-4 w-4" />
                                    Tambah Item
                                </Button>
                            </div>
                            <div class="mt-2 flex flex-col gap-3">
                                <div v-for="(item, index) in items" :key="index" class="rounded-lg border border-border p-3">
                                    <div class="flex items-start gap-2">
                                        <div class="flex-1 space-y-2">
                                            <Input v-model="item.title" placeholder="Judul" />
                                            <Textarea v-model="item.description" placeholder="Deskripsi" rows="2" />
                                        </div>
                                        <Button type="button" size="sm" variant="ghost" class="text-destructive" @click="removeItem(index)">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                                <p v-if="!items.length" class="text-sm text-muted-foreground">Belum ada item.</p>
                            </div>
                        </div>

                        <Button type="submit" :disabled="submitting">{{ submitting ? 'Menyimpan...' : 'Simpan Perubahan' }}</Button>
                    </form>
                </CardContent>
            </Card>
        </template>
    </AdminLayout>
</template>
