<script setup lang="ts">
import BackButton from '@/composables/ui/back-button/BackButton.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import Form from './components/ShowForm.vue';
import { Order } from './data/schema';

interface Props {
    order?: Order | null;
}

const props = defineProps<Props>();
const isEditMode = computed(() => props.order !== null && props.order !== undefined);

const breadcrumbs = computed((): BreadcrumbItem[] => {
    if (isEditMode.value && props.order?.id) {
        return [
            {
                title: trans('order.breadcrumb.index'),
                href: '/orders',
            },
            {
                title: trans('order.breadcrumb.edit'),
                href: `/orders/${props.order.id}/edit`,
            },
        ];
    }

    return [
        {
            title: trans('order.breadcrumb.index'),
            href: '/orders',
        },
        {
            title: trans('order.breadcrumb.create'),
            href: '/orders/create',
        },
    ];
});
</script>

<template>
    <Head :title="isEditMode ? 'Update Order' : 'Create Order'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <BackButton :href="route('orders.index')" :label="isEditMode ? trans('order.title.detail_order') : trans('order.title.create_order')" />
            <Form :order="order" />
        </div>
    </AppLayout>
</template>
