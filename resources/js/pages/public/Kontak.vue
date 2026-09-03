<script setup>
import { computed, onMounted } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import { useSettingsStore } from '@/stores/settings';
import { Clock, Mail, MapPin, Phone } from '@lucide/vue';

const settingsStore = useSettingsStore();

onMounted(() => settingsStore.fetchSettings());

const orgAddress = computed(() => settingsStore.settings?.org_address || 'Kampus Universitas Muhammadiyah Jambi');
const orgEmail = computed(() => settingsStore.settings?.org_email || 'ppid@umjambi.ac.id');
const orgPhone = computed(() => settingsStore.settings?.org_phone || '(0741) 60825');

const mapEmbedUrl = computed(() => `https://www.google.com/maps?q=${encodeURIComponent(orgAddress.value)}&output=embed`);
</script>

<template>
    <PublicLayout>
        <PageHeader title="Kontak & Lokasi" subtitle="Hubungi kami atau kunjungi langsung kantor PPID." />

        <div class="mx-auto max-w-5xl px-4 py-12">
            <div class="grid gap-8 lg:grid-cols-5">
                <div class="space-y-4 lg:col-span-2">
                    <div v-reveal="0" class="flex items-start gap-3 rounded-lg border border-border p-4">
                        <MapPin class="mt-0.5 h-5 w-5 shrink-0 text-primary" />
                        <div>
                            <p class="text-sm font-medium text-foreground">Alamat</p>
                            <p class="mt-1 text-sm text-muted-foreground">{{ orgAddress }}</p>
                        </div>
                    </div>
                    <div v-reveal="80" class="flex items-start gap-3 rounded-lg border border-border p-4">
                        <Mail class="mt-0.5 h-5 w-5 shrink-0 text-primary" />
                        <div>
                            <p class="text-sm font-medium text-foreground">Email</p>
                            <p class="mt-1 text-sm text-muted-foreground">{{ orgEmail }}</p>
                        </div>
                    </div>
                    <div v-reveal="160" class="flex items-start gap-3 rounded-lg border border-border p-4">
                        <Phone class="mt-0.5 h-5 w-5 shrink-0 text-primary" />
                        <div>
                            <p class="text-sm font-medium text-foreground">Telepon</p>
                            <p class="mt-1 text-sm text-muted-foreground">{{ orgPhone }}</p>
                        </div>
                    </div>
                    <div v-reveal="240" class="flex items-start gap-3 rounded-lg border border-border p-4">
                        <Clock class="mt-0.5 h-5 w-5 shrink-0 text-primary" />
                        <div>
                            <p class="text-sm font-medium text-foreground">Jam Layanan</p>
                            <p class="mt-1 text-sm text-muted-foreground">Senin – Jumat, 08.00 – 16.00 WIB</p>
                        </div>
                    </div>
                </div>

                <div v-reveal="120" class="overflow-hidden rounded-lg border border-border lg:col-span-3">
                    <iframe
                        :src="mapEmbedUrl"
                        title="Lokasi Kantor PPID"
                        class="h-80 w-full lg:h-full lg:min-h-[360px]"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    />
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
