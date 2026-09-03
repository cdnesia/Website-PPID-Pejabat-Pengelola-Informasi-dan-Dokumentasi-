<script setup>
import { ToastDescription, ToastPortal, ToastProvider, ToastRoot, ToastTitle, ToastViewport } from 'reka-ui';
import { AlertCircle, CheckCircle2, Info, X } from '@lucide/vue';
import { cn } from '@/lib/utils';
import { closeToast, removeToast, useToastState } from '@/lib/toast';

const state = useToastState();

const variantStyles = {
    default: 'border-border bg-card',
    success: 'border-l-4 border-l-accent bg-card',
    destructive: 'border-l-4 border-l-destructive bg-card',
};

const variantIcon = {
    default: Info,
    success: CheckCircle2,
    destructive: AlertCircle,
};

const variantIconClass = {
    default: 'text-muted-foreground',
    success: 'text-accent',
    destructive: 'text-destructive',
};
</script>

<template>
    <ToastProvider :duration="5000" swipe-direction="right">
        <ToastPortal>
            <ToastViewport class="fixed top-0 right-0 z-[100] m-0 flex w-full max-w-sm list-none flex-col gap-3 p-4 outline-none sm:top-auto sm:bottom-0">
                <ToastRoot
                    v-for="item in state.toasts"
                    :key="item.id"
                    :open="item.open"
                    :duration="item.duration"
                    :class="
                        cn(
                            'pointer-events-auto relative flex w-full items-start gap-3 rounded-lg border p-4 shadow-lg',
                            'data-[state=open]:animate-in data-[state=open]:slide-in-from-bottom-4 data-[state=open]:fade-in',
                            'data-[state=closed]:animate-out data-[state=closed]:fade-out data-[state=closed]:slide-out-to-right-full',
                            variantStyles[item.variant] ?? variantStyles.default,
                        )
                    "
                    @update:open="(value) => !value && closeToast(item.id)"
                    @escape-key-down="closeToast(item.id)"
                    @swipe-end="closeToast(item.id)"
                    @animationend="() => !item.open && removeToast(item.id)"
                >
                    <component :is="variantIcon[item.variant] ?? variantIcon.default" :class="cn('mt-0.5 h-5 w-5 shrink-0', variantIconClass[item.variant])" />
                    <div class="flex-1 space-y-1">
                        <ToastTitle v-if="item.title" class="text-sm font-semibold text-foreground">{{ item.title }}</ToastTitle>
                        <ToastDescription v-if="item.description" class="text-sm text-muted-foreground">{{ item.description }}</ToastDescription>
                    </div>
                    <button
                        type="button"
                        class="shrink-0 rounded-md p-1 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                        aria-label="Tutup notifikasi"
                        @click="closeToast(item.id)"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </ToastRoot>
            </ToastViewport>
        </ToastPortal>
    </ToastProvider>
</template>
