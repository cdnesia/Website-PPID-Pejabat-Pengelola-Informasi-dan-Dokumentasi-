<script setup>
import { computed } from 'vue';
import { ChevronLeft, ChevronRight } from '@lucide/vue';

const props = defineProps({
    currentPage: { type: Number, required: true },
    lastPage: { type: Number, required: true },
});
const emit = defineEmits(['update:page']);

const pages = computed(() => {
    const total = props.lastPage;
    const current = props.currentPage;

    if (total <= 7) {
        return Array.from({ length: total }, (_, i) => i + 1);
    }

    const keep = new Set([1, total, current - 1, current, current + 1]);
    const sorted = [...keep].filter((p) => p >= 1 && p <= total).sort((a, b) => a - b);

    const result = [];
    let previous = 0;
    for (const p of sorted) {
        if (previous && p - previous > 1) {
            result.push(`gap-${p}`);
        }
        result.push(p);
        previous = p;
    }
    return result;
});

function go(page) {
    if (typeof page !== 'number' || page === props.currentPage || page < 1 || page > props.lastPage) {
        return;
    }
    emit('update:page', page);
}
</script>

<template>
    <nav v-if="lastPage > 1" class="flex items-center justify-center gap-1.5" aria-label="Navigasi halaman">
        <button
            type="button"
            class="flex h-9 w-9 items-center justify-center rounded-full border border-border text-muted-foreground transition-colors hover:border-primary/40 hover:text-primary disabled:pointer-events-none disabled:opacity-40"
            :disabled="currentPage === 1"
            aria-label="Halaman sebelumnya"
            @click="go(currentPage - 1)"
        >
            <ChevronLeft class="h-4 w-4" />
        </button>

        <template v-for="p in pages" :key="p">
            <span v-if="typeof p === 'string'" class="px-1 text-sm text-muted-foreground">···</span>
            <button
                v-else
                type="button"
                class="flex h-9 min-w-9 items-center justify-center rounded-full px-3 text-sm font-medium transition-colors"
                :class="p === currentPage ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-muted'"
                :aria-current="p === currentPage ? 'page' : undefined"
                @click="go(p)"
            >
                {{ p }}
            </button>
        </template>

        <button
            type="button"
            class="flex h-9 w-9 items-center justify-center rounded-full border border-border text-muted-foreground transition-colors hover:border-primary/40 hover:text-primary disabled:pointer-events-none disabled:opacity-40"
            :disabled="currentPage === lastPage"
            aria-label="Halaman berikutnya"
            @click="go(currentPage + 1)"
        >
            <ChevronRight class="h-4 w-4" />
        </button>
    </nav>
</template>
