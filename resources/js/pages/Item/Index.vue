<script setup lang="ts">
import PageHeader from '@/composables/ui/page-header/PageHeader.vue';
import DataTable from '@/composables/ui/table/DataTable.vue';
import { PageData, TableInfo } from '@/interfaces/ITable';
import AppLayout from '@/layouts/AppLayout.vue';
import { columns } from '@/pages/Item/data/columns';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import { Company } from '../Company/data/schema';
import { toolbarActions } from './data/data';
import { Item } from './data/schema';

const props = defineProps<{
    data: PageData<Item>;
    companies: Company[];
}>();

const breadcrumbs = computed((): BreadcrumbItem[] => [
    {
        title: trans('item.bread_crumb.item'),
        href: '/items',
    },
]);
const companies_filter = computed(() =>
    props.companies.map((company) => ({
        value: company.value,
        label: company.label,
    })),
);

// Create filter configuration for companies
const filter_company = computed(() => ({
    title: 'Company',
    column: 'company_id',
    data: companies_filter.value,
}));

const tableInfo = computed(
    (): TableInfo => ({
        route: 'items.index',
        hidden_columns: ['created_at', 'updated_at'],
        button_toolbar: toolbarActions.value,
        pinned_left: ['item_id'],
        pinned_right: ['actions'],
        filter_toolbar: [filter_company.value],
    }),
);

const page = usePage();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Items" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <PageHeader :title="trans('item.title.title')" :description="trans('item.title.subtitle')" />
            <DataTable :data="data" :columns="columns" :tableInfo="tableInfo" />
        </div>
    </AppLayout>
</template>
