<script setup lang="ts">
import BackButton from '@/composables/ui/back-button/BackButton.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import CompanyForm from './components/CompanyForm.vue';
import { Company } from './data/schema';

interface Props {
    company?: Company | null;
}

const props = defineProps<Props>();
const isEditMode = computed(() => props.company !== null && props.company !== undefined);

const breadcrumbs = computed((): BreadcrumbItem[] => {
    if (isEditMode.value && props.company?.id) {
        return [
            {
                title: trans('company.breadcrumb.index'),
                href: '/companies',
            },
            {
                title: trans('company.breadcrumb.edit'),
                href: `/companies/${props.company.id}/edit`,
            },
        ];
    }

    return [
        {
            title: trans('company.breadcrumb.index'),
            href: '/companies',
        },
        {
            title: trans('company.breadcrumb.create'),
            href: '/companies/create',
        },
    ];
});
</script>

<template>
    <Head :title="isEditMode ? 'Update Company' : 'Create Company'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <BackButton
                :href="route('companies.index')"
                :label="isEditMode ? trans('company.title.update_company') : trans('company.title.create_company')"
            />
            <CompanyForm :company="company" />
        </div>
    </AppLayout>
</template>
