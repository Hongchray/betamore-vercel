<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell class="h-screen flex-col overflow-hidden">
        <AppHeader :breadcrumbs="breadcrumbs" class="flex-shrink-0" />
        <AppContent class="flex-1 overflow-x-hidden overflow-y-auto">
            <slot />
        </AppContent>
    </AppShell>
</template>
