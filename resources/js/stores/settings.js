import { defineStore } from 'pinia';
import api from '@/lib/axios';

export const useSettingsStore = defineStore('settings', {
    state: () => ({
        settings: null,
        initialized: false,
    }),
    actions: {
        async fetchSettings() {
            if (this.initialized) return;

            try {
                const { data } = await api.get('/settings');
                this.settings = data.data;
            } finally {
                this.initialized = true;
            }
        },
    },
});
