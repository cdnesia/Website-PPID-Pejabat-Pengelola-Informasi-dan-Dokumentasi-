<script setup>
defineProps({
    icon: { type: [Object, Function], required: true },
    message: { type: String, required: true },
});
</script>

<template>
    <div class="flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-secondary/40 bg-secondary/5 px-6 py-16 text-center">
        <component :is="icon" class="h-10 w-10 text-secondary" />
        <p class="text-sm text-muted-foreground">{{ message }}</p>
    </div>
</template>
