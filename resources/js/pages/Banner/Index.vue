<script setup lang="ts">
import PageHeader from '@/composables/ui/page-header/PageHeader.vue';
import DataTable from '@/composables/ui/table/DataTable.vue';
import { PageData, TableInfo } from '@/interfaces/ITable';
import AppLayout from '@/layouts/AppLayout.vue';
import { columns } from '@/pages/Banner/data/columns';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import { toolbarActions } from './data/data';
import { Banner } from './data/schema';

const props = defineProps<{
    data: PageData<Banner>;
}>();

const tableInfo = computed(
    (): TableInfo => ({
        route: 'banners.index',
        hidden_columns: ['created_at', 'updated_at'],
        button_toolbar: toolbarActions.value,
        pinned_left: ['banner_id'],
        pinned_right: ['actions'],
    }),
);

const breadcrumbs = computed((): BreadcrumbItem[] => [
    {
        title: trans('banner.breadcrumb.index'),
        href: '/banners',
    },
]);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Banners" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <PageHeader :title="trans('banner.title.title')" :description="trans('banner.title.subtitle')" />
            <DataTable :data="data" :columns="columns" :tableInfo="tableInfo" />
        </div>
    </AppLayout>
</template>
