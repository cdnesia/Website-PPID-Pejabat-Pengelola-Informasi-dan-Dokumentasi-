<script setup>
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { toast } from '@/lib/toast';
import { Copy, MessageCircle, Send } from '@lucide/vue';

const props = defineProps({
    title: { type: String, required: true },
});

const copied = ref(false);

function shareWhatsapp() {
    const text = `${props.title} - ${window.location.href}`;
    window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank', 'noopener,noreferrer');
}

function shareTelegram() {
    const url = `https://t.me/share/url?url=${encodeURIComponent(window.location.href)}&text=${encodeURIComponent(props.title)}`;
    window.open(url, '_blank', 'noopener,noreferrer');
}

async function copyLink() {
    try {
        await navigator.clipboard.writeText(window.location.href);
        copied.value = true;
        toast({ title: 'Tautan disalin', description: 'Tautan halaman ini sudah disalin ke clipboard.', variant: 'success' });
        setTimeout(() => (copied.value = false), 2000);
    } catch {
        toast({ title: 'Gagal menyalin tautan', description: 'Salin tautan secara manual dari address bar.', variant: 'destructive' });
    }
}
</script>

<template>
    <div class="flex flex-wrap items-center gap-2">
        <span class="text-sm font-medium text-muted-foreground">Bagikan:</span>
        <Button variant="outline" size="sm" @click="shareWhatsapp">
            <MessageCircle class="h-4 w-4 text-[#25D366]" />
            WhatsApp
        </Button>
        <Button variant="outline" size="sm" @click="shareTelegram">
            <Send class="h-4 w-4 text-[#229ED9]" />
            Telegram
        </Button>
        <Button variant="outline" size="sm" @click="copyLink">
            <Copy class="h-4 w-4" />
            {{ copied ? 'Tautan disalin!' : 'Salin Tautan' }}
        </Button>
    </div>
</template>
