<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar" class="flex h-full overflow-hidden">
        <AppSidebar class="flex-shrink-0" />

        <AppContent variant="sidebar" class="flex min-w-0 flex-1 flex-col overflow-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" class="flex-shrink-0" />
            <div class="flex-1 overflow-x-hidden overflow-y-auto">
                <slot />
            </div>
        </AppContent>
    </AppShell>
</template>
