<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import LanguageSwitcher from '@/composables/LanguageSwitcher.vue';
import type { BreadcrumbItemType } from '@/types';
withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header class="flex h-16 items-center justify-between border-b border-muted px-4 transition-all md:px-6">
        <!-- Left side: Sidebar + Breadcrumbs -->
        <div class="flex items-center gap-3">
            <SidebarTrigger class="-ml-1" />

            <Breadcrumbs v-if="breadcrumbs?.length" :breadcrumbs="breadcrumbs" />
        </div>

        <!-- Right side: Language Switcher -->
        <div class="flex items-center gap-3">
            <LanguageSwitcher />
        </div>
    </header>
</template>
