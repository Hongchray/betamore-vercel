<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const siteInfo = page.props.siteInfo as {
    site_name: string;
    logo_url?: string;
    favicon_url?: string;
    meta_description?: string;
};
</script>

<template>
    <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary p-0 text-sidebar-primary-foreground">
        <AppLogoIcon :src="siteInfo?.logo_url" :alt="siteInfo?.site_name || 'Logo'" class="size-8 fill-current text-white dark:text-black" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">
            {{ siteInfo?.site_name || 'Focuz Solution' }}
        </span>
    </div>
</template>
