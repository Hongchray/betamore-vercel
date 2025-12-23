<script setup lang="ts">
import PageHeader from '@/composables/ui/page-header/PageHeader.vue';
import DataTable from '@/composables/ui/table/DataTable.vue';
import { PageData, TableInfo } from '@/interfaces/ITable';
import AppLayout from '@/layouts/AppLayout.vue';
import { columns } from '@/pages/Customer/data/columns';
import { toolbarActions } from '@/pages/Customer/data/data';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import { gender_options } from './data/data';
import { User } from './data/schema';

const props = defineProps<{
    data: PageData<User>;
}>();

const filter_gender = computed(() => ({
    title: 'Gender',
    column: 'gender',
    data: gender_options.value,
}));

const tableInfo = computed(
    (): TableInfo => ({
        route: 'customers.index',
        hidden_columns: ['created_at', 'updated_at'],
        button_toolbar: toolbarActions.value,
        filter_toolbar: [filter_gender.value],
    }),
);

const breadcrumbs = computed((): BreadcrumbItem[] => [
    {
        title: trans('customer.breadcrumb.index'),
        href: '/customers',
    },
]);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Customer" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <PageHeader :title="trans('customer.title.title')" :description="trans('customer.title.subtitle')" />
            <DataTable :data="data" :columns="columns" :tableInfo="tableInfo" />
        </div>
    </AppLayout>
</template>
