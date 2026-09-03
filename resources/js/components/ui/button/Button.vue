<script setup>
import { cn } from '@/lib/utils';

const props = defineProps({
    variant: { type: String, default: 'default' },
    size: { type: String, default: 'default' },
    as: { type: String, default: 'button' },
    class: { type: String, default: '' },
});

const variants = {
    default: 'bg-primary text-primary-foreground shadow-sm hover:bg-primary/90 hover:shadow-md',
    secondary: 'bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/90 hover:shadow-md',
    accent: 'bg-accent text-accent-foreground shadow-sm hover:bg-accent/90 hover:shadow-md',
    destructive: 'bg-destructive text-destructive-foreground shadow-sm hover:bg-destructive/90 hover:shadow-md',
    outline: 'border border-input bg-background hover:border-primary/40 hover:bg-muted',
    ghost: 'hover:bg-muted',
    link: 'text-primary underline-offset-4 hover:underline',
};

const sizes = {
    default: 'h-10 px-4 py-2',
    sm: 'h-9 rounded-md px-3',
    lg: 'h-11 rounded-md px-8',
    icon: 'h-10 w-10',
};
</script>

<template>
    <component
        :is="as"
        :class="
            cn(
                'inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium transition-all duration-150 active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:active:scale-100',
                variants[props.variant],
                sizes[props.size],
                props.class,
            )
        "
    >
        <slot />
    </component>
</template>
