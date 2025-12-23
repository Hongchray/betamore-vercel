<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
    {
        title: trans('setting.appearance.title'),
        href: '/settings/appearance',
    },
]);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Appearance settings" />

        <SettingsLayout class="max-w-3xl">
            <div class="space-y-6">
                <HeadingSmall :title="trans('setting.appearance.title')" :description="trans('setting.appearance.description')" />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
