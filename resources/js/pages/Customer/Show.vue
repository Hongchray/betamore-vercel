<script setup lang="ts">
import BackButton from '@/composables/ui/back-button/BackButton.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import Form from './components/ViewForm.vue';
import { User } from './data/schema';
interface ComboboxItem {
    value: string;
    label: string;
}
interface Props {
    user?: User | null;
    order_count: number;
    total_paid: number;
}

const props = defineProps<Props>();
const isEditMode = computed(() => props.user !== null && props.user !== undefined);

const breadcrumbs = computed((): BreadcrumbItem[] => {
    return [
        {
            title: trans('customer.breadcrumb.index'),
            href: '/customers',
        },
        {
            title: trans('customer.breadcrumb.show'),
            href: props.user ? `/customers/${props.user.id}/show` : '/customers',
        },
    ];
});
</script>

<template>
    <Head :title="isEditMode ? 'Edit Customer' : 'Create Customer'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <BackButton
                :href="route('customers.index')"
                :label="isEditMode ? trans('customer.title.update_user') : trans('customer.title.create_user')"
            />
            <Form :user="user" :total_paid="total_paid" :order_count="order_count" />
        </div>
    </AppLayout>
</template>
