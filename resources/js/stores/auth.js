import { defineStore } from 'pinia';
import api, { ensureCsrfCookie } from '@/lib/axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        initialized: false,
    }),
    getters: {
        isAuthenticated: (state) => state.user !== null,
        roles: (state) => state.user?.roles ?? [],
        isAdmin: (state) =>
            state.roles.some((role) =>
                ['super_admin', 'admin_ppid_utama', 'admin_ppid_pembantu', 'pimpinan'].includes(role),
            ),
    },
    actions: {
        async fetchUser() {
            try {
                const { data } = await api.get('/auth/me');
                this.user = data.data;
            } catch {
                this.user = null;
            } finally {
                this.initialized = true;
            }
        },
        async login(credentials) {
            await ensureCsrfCookie();
            const { data } = await api.post('/auth/login', credentials);
            this.user = data.data;
        },
        async register(payload) {
            await ensureCsrfCookie();
            const { data } = await api.post('/auth/register', payload);
            this.user = data.data;
        },
        async logout() {
            await api.post('/auth/logout');
            this.user = null;
        },
    },
});
