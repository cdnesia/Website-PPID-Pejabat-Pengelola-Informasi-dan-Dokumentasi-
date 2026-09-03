<script setup>
import { cn } from '@/lib/utils';

const props = defineProps({
    variant: { type: String, default: 'default' },
    class: { type: String, default: '' },
});

const variants = {
    default: 'bg-primary text-primary-foreground',
    secondary: 'bg-secondary text-secondary-foreground',
    accent: 'bg-accent text-accent-foreground',
    destructive: 'bg-destructive text-destructive-foreground',
    outline: 'border border-input text-foreground',
};
</script>

<template>
    <span
        :class="
            cn(
                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset ring-black/5',
                variants[props.variant],
                props.class,
            )
        "
    >
        <slot />
    </span>
</template>
