<script setup lang="ts">
import PageHeader from '@/composables/ui/page-header/PageHeader.vue';
import DataTable from '@/composables/ui/table/DataTable.vue';
import { PageData, TableInfo } from '@/interfaces/ITable';
import AppLayout from '@/layouts/AppLayout.vue';
import { payment_status, toolbarActions } from '@/pages/Order/data/data'; // If this is generic toolbar, keep or move accordingly
import { columns } from '@/pages/Payment/data/columns';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import { Payment } from './data/schema';

const props = defineProps<{
    data: PageData<Payment>;
    payment_methods: { id: number; name: string }[];
}>();

const payment_methods = computed(() =>
    props.payment_methods.map((method: { id: number; name: string }) => ({
        value: String(method.id),
        label: method.name,
    })),
);

const payment_status_filter = computed(() => ({
    title: 'Payment Method',
    column: 'payment_method_id',
    data: payment_methods.value,
}));
const filter_payment_status = computed(() => ({
    title: 'Payment Status',
    column: 'payment_status',
    data: payment_status.value,
}));

const tableInfo = computed(
    (): TableInfo => ({
        route: 'payments.index',
        hidden_columns: ['order_note', 'created_at', 'updated_at'],
        button_toolbar: toolbarActions.value,
        filter_toolbar: [filter_payment_status.value, payment_status_filter.value],
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
        title: trans('order.index.breadcrumb'),
        href: '/orders',
    },
]);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Payment" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <PageHeader :title="trans('order.title.payment')" :description="trans('order.title.paymen_subtitle')" />
            <DataTable :data="data" :columns="columns" :tableInfo="tableInfo" />
        </div>
    </AppLayout>
</template>
