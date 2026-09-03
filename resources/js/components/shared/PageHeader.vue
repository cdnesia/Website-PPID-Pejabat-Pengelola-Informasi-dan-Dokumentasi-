<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <section class="border-b border-border bg-gradient-to-b from-primary/10 via-secondary/5 to-background">
        <div class="mx-auto max-w-6xl px-4 py-12 text-center sm:py-16">
            <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">{{ title }}</h1>
            <span class="mx-auto mt-3 block h-1 w-14 rounded-full bg-primary" />
            <p v-if="subtitle" class="mx-auto mt-4 max-w-2xl text-sm text-muted-foreground">{{ subtitle }}</p>
        </div>
    </section>
</template>
