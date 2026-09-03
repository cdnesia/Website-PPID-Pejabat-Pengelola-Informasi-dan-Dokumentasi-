<script setup>
import { cn } from '@/lib/utils';

defineProps({ class: { type: String, default: '' } });
defineOptions({ inheritAttrs: false });

const model = defineModel({ type: String, default: '' });
</script>

<template>
    <textarea
        v-model="model"
        v-bind="$attrs"
        :class="
            cn(
                'flex min-h-24 w-full rounded-md border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50',
                $props.class,
            )
        "
    />
</template>
