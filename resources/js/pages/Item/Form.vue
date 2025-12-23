<script setup lang="ts">
import BackButton from '@/composables/ui/back-button/BackButton.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Form from '@/pages/Item/components/Form.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import { type Item } from './data/schema';
interface ComboboxItem {
    value: string;
    label: string;
}
interface Props {
    item?: Item | null;
    companies: ComboboxItem[];
}

const props = defineProps<Props>();
const isEditMode = computed(() => props.item !== null && props.item !== undefined);

const breadcrumbs = computed((): BreadcrumbItem[] => {
    if (isEditMode.value && props.item?.id) {
        return [
            {
                title: trans('item.bread_crumb.item'),
                href: '/items',
            },
            {
                title: trans('item.bread_crumb.item_update'),
                href: `/items/${props.item.id}/edit`,
            },
        ];
    }

    return [
        {
            title: trans('item.bread_crumb.item'),
            href: '/items',
        },
        {
            title: trans('item.bread_crumb.item_create'),
            href: `/items/create`,
        },
    ];
});

const pageTitle = computed(() => (isEditMode.value ? 'Edit Item' : 'Create Item'));
</script>

<template>
    <Head :title="pageTitle" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <BackButton :href="route('items.index')" :label="isEditMode ? trans('item.title.update_item') : trans('item.title.create_item')" />
            <Form :item="item" :companies="companies" />
        </div>
    </AppLayout>
</template>
