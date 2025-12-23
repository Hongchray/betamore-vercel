<script setup lang="ts">
import PageHeader from '@/composables/ui/page-header/PageHeader.vue';
import DataTable from '@/composables/ui/table/DataTable.vue';
import { PageData, TableInfo } from '@/interfaces/ITable';
import AppLayout from '@/layouts/AppLayout.vue';
import { columns } from '@/pages/Promotion/data/columns';
import { toolbarActions } from '@/pages/Promotion/data/data';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { CheckCircle, Clock, XCircle } from 'lucide-vue-next';
import { computed } from 'vue';
import { Promotion } from './data/schema';

const props = defineProps<{
    data: PageData<Promotion>;
}>();

const status = computed(() => [
    {
        value: 'upcoming',
        label: 'Upcoming',
        icon: Clock,
    },
    {
        value: 'active',
        label: 'Active',
        icon: CheckCircle,
    },
    {
        value: 'expired',
        label: 'Expired',
        icon: XCircle,
    },
]);

const filter_status = computed(() => ({
    title: 'Status',
    column: 'status',
    data: status.value,
}));

const tableInfo = computed(
    (): TableInfo => ({
        route: 'promotions.index',
        hidden_columns: ['created_at', 'updated_at', 'start_date', 'end_date', 'start_time', 'end_time', 'description_en', 'description_km'],
        button_toolbar: toolbarActions.value,
        filter_toolbar: [filter_status.value],
        pinned_left: ['promotion_id'],
        pinned_right: ['actions'],
    }),
);

const breadcrumbs = computed((): BreadcrumbItem[] => [
    {
        title: trans('promotion.breadcrumb.index'),
        href: '/promotions',
    },
]);
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Promotions" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <PageHeader :title="trans('promotion.title.title')" :description="trans('promotion.title.subtitle')" />
            <DataTable :data="data" :columns="columns" :tableInfo="tableInfo" />
        </div>
    </AppLayout>
</template>
