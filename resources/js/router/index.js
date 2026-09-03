import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const SITE_NAME = 'PPID UM Jambi';

const routes = [
    { path: '/', name: 'beranda', component: () => import('@/pages/public/Beranda.vue'), meta: { title: 'Beranda' } },
    {
        path: '/informasi-publik',
        name: 'informasi-publik',
        component: () => import('@/pages/public/InformasiPublik.vue'),
        meta: { title: 'Daftar Informasi Publik' },
    },
    {
        path: '/informasi-publik/:slug',
        name: 'informasi-detail',
        component: () => import('@/pages/public/InformasiDetail.vue'),
        meta: { title: 'Informasi Publik' },
    },
    {
        path: '/permohonan/buat',
        name: 'permohonan-buat',
        component: () => import('@/pages/public/PermohonanBuat.vue'),
        meta: { title: 'Permohonan Informasi' },
    },
    {
        path: '/permohonan/lacak/:nomor?',
        name: 'permohonan-lacak',
        component: () => import('@/pages/public/PermohonanLacak.vue'),
        meta: { title: 'Lacak Permohonan' },
    },
    {
        path: '/keberatan',
        name: 'keberatan',
        component: () => import('@/pages/public/Keberatan.vue'),
        meta: { requiresAuth: true, title: 'Ajukan Keberatan' },
    },
    { path: '/berita', name: 'berita', component: () => import('@/pages/public/Berita.vue'), meta: { title: 'Berita & Pengumuman' } },
    {
        path: '/berita/:slug',
        name: 'berita-detail',
        component: () => import('@/pages/public/BeritaDetail.vue'),
        meta: { title: 'Berita' },
    },
    { path: '/tentang-ppid', name: 'tentang', component: () => import('@/pages/public/Tentang.vue'), meta: { title: 'Tentang PPID' } },
    {
        path: '/visi-misi',
        name: 'visi-misi',
        component: () => import('@/pages/public/VisiMisi.vue'),
        meta: { title: 'Visi dan Misi' },
    },
    {
        path: '/struktur-organisasi',
        name: 'struktur-organisasi',
        component: () => import('@/pages/public/StrukturOrganisasi.vue'),
        meta: { title: 'Struktur Organisasi' },
    },
    {
        path: '/tugas-fungsi',
        name: 'tugas-fungsi',
        component: () => import('@/pages/public/TugasFungsi.vue'),
        meta: { title: 'Tugas Pokok dan Fungsi' },
    },
    {
        path: '/dasar-hukum',
        name: 'dasar-hukum',
        component: () => import('@/pages/public/DasarHukum.vue'),
        meta: { title: 'Dasar Hukum' },
    },
    {
        path: '/alur-layanan',
        name: 'alur-layanan',
        component: () => import('@/pages/public/AlurLayanan.vue'),
        meta: { title: 'Alur Layanan' },
    },
    { path: '/faq', name: 'faq', component: () => import('@/pages/public/Faq.vue'), meta: { title: 'FAQ' } },
    { path: '/kontak', name: 'kontak', component: () => import('@/pages/public/Kontak.vue'), meta: { title: 'Kontak' } },

    {
        path: '/auth/login',
        name: 'login',
        component: () => import('@/pages/auth/Login.vue'),
        meta: { guestOnly: true, title: 'Masuk' },
    },
    {
        path: '/auth/register',
        name: 'register',
        component: () => import('@/pages/auth/Register.vue'),
        meta: { guestOnly: true, title: 'Daftar' },
    },
    {
        path: '/auth/forgot-password',
        name: 'forgot-password',
        component: () => import('@/pages/auth/ForgotPassword.vue'),
        meta: { guestOnly: true, title: 'Lupa Kata Sandi' },
    },

    {
        path: '/admin/dashboard',
        name: 'admin-dashboard',
        component: () => import('@/pages/admin/Dashboard.vue'),
        meta: { requiresAdmin: true, title: 'Dashboard Admin' },
    },
    {
        path: '/admin/permohonan',
        name: 'admin-permohonan',
        component: () => import('@/pages/admin/Permohonan.vue'),
        meta: { requiresAdmin: true, title: 'Manajemen Permohonan' },
    },
    {
        path: '/admin/dokumen',
        name: 'admin-dokumen',
        component: () => import('@/pages/admin/Dokumen.vue'),
        meta: { requiresAdmin: true, title: 'Manajemen Dokumen' },
    },
    {
        path: '/admin/berita',
        name: 'admin-berita',
        component: () => import('@/pages/admin/Berita.vue'),
        meta: { requiresAdmin: true, title: 'Manajemen Berita' },
    },
    {
        path: '/admin/halaman-profil',
        name: 'admin-halaman-profil',
        component: () => import('@/pages/admin/HalamanProfil.vue'),
        meta: { requiresAdmin: true, title: 'Halaman Profil' },
    },
    {
        path: '/admin/keberatan',
        name: 'admin-keberatan',
        component: () => import('@/pages/admin/Keberatan.vue'),
        meta: { requiresAdmin: true, title: 'Manajemen Keberatan' },
    },
    {
        path: '/admin/laporan',
        name: 'admin-laporan',
        component: () => import('@/pages/admin/Laporan.vue'),
        meta: { requiresAdmin: true, title: 'Laporan' },
    },
    {
        path: '/admin/pengguna',
        name: 'admin-pengguna',
        component: () => import('@/pages/admin/Pengguna.vue'),
        meta: { requiresAdmin: true, title: 'Manajemen Pengguna' },
    },
    {
        path: '/admin/pengaturan',
        name: 'admin-pengaturan',
        component: () => import('@/pages/admin/Pengaturan.vue'),
        meta: { requiresAdmin: true, title: 'Pengaturan' },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        }
        if (to.hash) {
            return { el: to.hash, behavior: 'smooth', top: 88 };
        }
        return { top: 0 };
    },
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();

    if (!auth.initialized) {
        await auth.fetchUser();
    }

    if ((to.meta.requiresAuth || to.meta.requiresAdmin) && !auth.isAuthenticated) {
        return { name: 'login', query: { redirect: to.fullPath } };
    }

    if (to.meta.requiresAdmin && !auth.isAdmin) {
        return { name: 'beranda' };
    }

    if (to.meta.guestOnly && auth.isAuthenticated) {
        return { name: 'beranda' };
    }

    return true;
});

router.afterEach((to) => {
    document.title = to.meta.title ? `${to.meta.title} — ${SITE_NAME}` : SITE_NAME;
});

export default router;
