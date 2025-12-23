<script setup lang="ts">
import PageHeader from '@/composables/ui/page-header/PageHeader.vue';
import DataTable from '@/composables/ui/table/DataTable.vue';
import { PageData, TableInfo } from '@/interfaces/ITable';
import AppLayout from '@/layouts/AppLayout.vue';
import { columns } from '@/pages/Delivery/data/columns';
import { toolbarActions } from '@/pages/Delivery/data/data'; // If this is generic toolbar, keep or move accordingly
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { CheckCircle, XCircle } from 'lucide-vue-next';
import { computed } from 'vue';
import { Delivery } from './data/schema';

const props = defineProps<{
    data: PageData<Delivery>;
}>();

const status = computed(() => [
    {
        value: '1',
        label: 'Active',
        icon: CheckCircle,
    },
    {
        value: '0',
        label: 'Inactive',
        icon: XCircle,
    },
]);

const filter_status = computed(() => ({
    title: 'Status',
    column: 'is_active',
    data: status.value,
}));

const tableInfo = computed(
    (): TableInfo => ({
        route: 'deliveries.index',
        hidden_columns: ['created_at', 'updated_at'],
        button_toolbar: toolbarActions.value,
        pinned_left: ['delivery_id'],
        filter_toolbar: [filter_status.value],
        pinned_right: ['actions'],
    }),
);

const breadcrumbs = computed((): BreadcrumbItem[] => [
    {
        title: trans('delivery.index.breadcrumb'),
        href: '/deliveries',
    },
]);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Delivery" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <PageHeader :title="trans('delivery.index.title')" :description="trans('delivery.index.subtitle')" />
            <DataTable :data="data" :columns="columns" :tableInfo="tableInfo" />
        </div>
    </AppLayout>
</template>
