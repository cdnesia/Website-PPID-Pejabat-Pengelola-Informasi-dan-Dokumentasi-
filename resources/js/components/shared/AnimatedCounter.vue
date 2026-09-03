<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    value: { type: Number, required: true },
    duration: { type: Number, default: 1000 },
});

const el = ref(null);
const display = ref(0);
let hasIntersected = false;
let observer;
let rafId;

function animateTo(target) {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        display.value = target;
        return;
    }

    if (rafId) cancelAnimationFrame(rafId);

    const from = display.value;
    const start = performance.now();

    function step(now) {
        const progress = Math.min((now - start) / props.duration, 1);
        const eased = 1 - (1 - progress) ** 3;
        display.value = Math.round(from + (target - from) * eased);
        if (progress < 1) {
            rafId = requestAnimationFrame(step);
        }
    }

    rafId = requestAnimationFrame(step);
}

watch(
    () => props.value,
    (newValue) => {
        if (hasIntersected) animateTo(newValue);
    },
);

onMounted(() => {
    observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting && !hasIntersected) {
                    hasIntersected = true;
                    animateTo(props.value);
                    observer.disconnect();
                }
            });
        },
        { threshold: 0.3 },
    );
    if (el.value) observer.observe(el.value);
});

onUnmounted(() => {
    observer?.disconnect();
    if (rafId) cancelAnimationFrame(rafId);
});
</script>

<template>
    <span ref="el">{{ display }}</span>
</template>
