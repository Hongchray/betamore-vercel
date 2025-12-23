<script setup lang="ts">
import PageHeader from '@/composables/ui/page-header/PageHeader.vue';
import DataTable from '@/composables/ui/table/DataTable.vue';
import { PageData, TableInfo } from '@/interfaces/ITable';
import AppLayout from '@/layouts/AppLayout.vue';
import { columns } from '@/pages/Report/data/columns';
import { toolbarActions } from '@/pages/Report/data/data';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import { Report } from './data/schema';

const props = defineProps<{
    data: PageData<Report>;
}>();

const tableInfo = computed(
    (): TableInfo => ({
        route: 'reports.index',
        hidden_columns: ['order_note', 'created_at', 'updated_at'],
        button_toolbar: toolbarActions.value,
        pinned_left: ['order_number'],
        pinned_right: ['actions'],
        date_range_filter: {
            start_date: 'created_at',
            end_date: 'created_at',
        },
    }),
);

const breadcrumbs = computed((): BreadcrumbItem[] => [
    {
        title: trans('report.index.breadcrumb'),
        href: '/orders',
    },
]);
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Payment" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <PageHeader :title="trans('report.title.payment')" :description="trans('report.title.payment_subtitle')" />
            <DataTable :data="data" :columns="columns" :tableInfo="tableInfo" />
        </div>
    </AppLayout>
</template>
