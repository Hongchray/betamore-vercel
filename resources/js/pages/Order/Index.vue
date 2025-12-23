<script setup lang="ts">
import PageHeader from '@/composables/ui/page-header/PageHeader.vue';
import DataTable from '@/composables/ui/table/DataTable.vue';
import { PageData, TableInfo } from '@/interfaces/ITable';
import AppLayout from '@/layouts/AppLayout.vue';
import { columns } from '@/pages/Order/data/columns';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import { Delivery } from '../Delivery/data/schema';
import { order_status, payment_status } from './data/data';
import { Order } from './data/schema';

const props = defineProps<{
    data: PageData<Order>;
    deliveries: Delivery[];
    payment_methods: { id: number; name: string }[];
}>();

const deliveries_filter = computed(() =>
    props.deliveries.map((delivery) => ({
        value: delivery.id,
        label: delivery.name,
    })),
);

const payment_methods = computed(() =>
    props.payment_methods.map((method: {}) => ({
        value: method.id,
        label: method.name,
    })),
);

const filter_delivery = computed(() => ({
    title: 'Delivery',
    column: 'delivery_id',
    data: deliveries_filter.value,
}));
const filter_status = computed(() => ({
    title: 'Order Status',
    column: 'status',
    data: order_status.value,
}));
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
        route: 'orders.index',
        hidden_columns: ['created_at', 'updated_at', 'note'],
        button_toolbar: [],
        pinned_left: ['order_number'],
        pinned_right: ['actions'],
        filter_toolbar: [filter_delivery.value, filter_status.value, filter_payment_status.value, payment_status_filter.value],
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
        <Head title="Order" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <PageHeader :title="trans('order.index.title')" :description="trans('order.index.subtitle')" />
            <DataTable :data="data" :columns="columns" :tableInfo="tableInfo" />
        </div>
    </AppLayout>
</template>
