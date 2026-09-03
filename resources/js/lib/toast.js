import { reactive } from 'vue';

const state = reactive({ toasts: [] });
let nextId = 0;

export function useToastState() {
    return state;
}

export function toast({ title, description, variant = 'default', duration = 5000 }) {
    const id = ++nextId;
    state.toasts.push({ id, title, description, variant, duration, open: true });
    return id;
}

export function closeToast(id) {
    const item = state.toasts.find((t) => t.id === id);
    if (item) item.open = false;
}

export function removeToast(id) {
    state.toasts = state.toasts.filter((t) => t.id !== id);
}
