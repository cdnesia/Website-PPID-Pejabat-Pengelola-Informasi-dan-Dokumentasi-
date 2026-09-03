<script setup>
import { ref } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { DropdownMenuContent, DropdownMenuItem, DropdownMenuPortal, DropdownMenuRoot, DropdownMenuTrigger } from 'reka-ui';
import { ChevronDown } from '@lucide/vue';
import { isLinkActive, parseLinkTarget } from '@/lib/nav';

const props = defineProps({
    label: { type: String, required: true },
    links: { type: Array, required: true },
});

const route = useRoute();
const open = ref(false);

const isActive = () => props.links.some((link) => route.path === parseLinkTarget(link.to).path);
</script>

<template>
    <DropdownMenuRoot v-model:open="open">
        <DropdownMenuTrigger
            class="flex h-full items-center gap-1 border-b-2 border-transparent px-3 text-sm font-semibold text-muted-foreground transition-colors outline-none hover:text-foreground"
            :class="isActive() && 'border-primary text-primary hover:text-primary'"
        >
            {{ label }}
            <ChevronDown class="h-3.5 w-3.5 transition-transform" :class="open && 'rotate-180'" />
        </DropdownMenuTrigger>
        <DropdownMenuPortal>
            <DropdownMenuContent class="z-50 min-w-[240px] rounded-md border border-border bg-card p-1 shadow-lg" align="start" :side-offset="8">
                <DropdownMenuItem v-for="link in links" :key="link.to" as-child>
                    <RouterLink
                        :to="link.to"
                        class="block rounded-sm px-3 py-2 text-sm text-foreground outline-none transition-colors hover:bg-muted"
                        :class="isLinkActive(route, link.to) && 'bg-primary/10 text-primary hover:bg-primary/10'"
                    >
                        {{ link.label }}
                    </RouterLink>
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenuPortal>
    </DropdownMenuRoot>
</template>
