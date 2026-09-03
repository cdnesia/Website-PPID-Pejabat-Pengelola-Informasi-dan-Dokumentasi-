<script setup>
import { cn } from '@/lib/utils';

const props = defineProps({ class: { type: String, default: '' } });
</script>

<template>
    <div
        :class="
            cn(
                'rounded-xl border border-border bg-card text-card-foreground shadow-sm transition-shadow duration-200',
                props.class,
            )
        "
    >
        <slot />
    </div>
</template>
