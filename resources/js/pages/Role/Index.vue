<script setup lang="ts">
import PageHeader from '@/composables/ui/page-header/PageHeader.vue';
import DataTable from '@/composables/ui/table/DataTable.vue';
import { PageData, TableInfo } from '@/interfaces/ITable';
import AppLayout from '@/layouts/AppLayout.vue';
import { columns } from '@/pages/Role/data/columns';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import { toolbarActions } from './data/data';
import { Role } from './data/schema';

const props = defineProps<{
    data: PageData<Role>;
}>();

// Follow the same pattern as Profile.vue
const breadcrumbs = computed((): BreadcrumbItem[] => [
    {
        title: trans('role.bread_crumb.role'),
        href: '/roles',
    },
]);

const tableInfo = computed(
    (): TableInfo => ({
        route: 'roles.index',
        hidden_columns: [],
        button_toolbar: toolbarActions.value,
    }),
);

const page = usePage();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Roles" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <PageHeader :title="trans('role.title.title')" :description="trans('role.title.subtitle')" />
            <DataTable :data="data" :columns="columns" :tableInfo="tableInfo" />
        </div>
    </AppLayout>
</template>
