<script setup lang="ts">
import BackButton from '@/composables/ui/back-button/BackButton.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import Form from './components/DeliveryForm.vue';
import { Delivery } from './data/schema';

interface Props {
    delivery?: Delivery | null;
}

const props = defineProps<Props>();
const isEditMode = computed(() => props.delivery !== null && props.delivery !== undefined);

const breadcrumbs = computed((): BreadcrumbItem[] => {
    if (isEditMode.value && props.delivery?.id) {
        return [
            {
                title: trans('delivery.breadcrumb.index'),
                href: '/deliveries',
            },
            {
                title: trans('delivery.breadcrumb.edit'),
                href: `/deliveries/${props.delivery.id}/edit`,
            },
        ];
    }

    return [
        {
            title: trans('delivery.breadcrumb.index'),
            href: '/deliveries',
        },
        {
            title: trans('delivery.breadcrumb.create'),
            href: '/deliveries/create',
        },
    ];
});
</script>

<template>
    <Head :title="isEditMode ? 'Update Delivery' : 'Create Delivery'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <BackButton
                :href="route('deliveries.index')"
                :label="isEditMode ? trans('delivery.title.update_delivery') : trans('delivery.title.create_delivery')"
            />
            <Form :delivery="delivery" />
        </div>
    </AppLayout>
</template>
