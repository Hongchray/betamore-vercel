<script setup lang="ts">
import BackButton from '@/composables/ui/back-button/BackButton.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import PromotionForm from './components/PromotionForm.vue';

const props = defineProps<{
    promotion?: any;
    items: any[];
}>();

const breadcrumbs = computed((): BreadcrumbItem[] => [
    {
        title: trans('promotion.breadcrumb.index'),
        href: '/promotions',
    },
    {
        title: props.promotion ? trans('promotion.breadcrumb.edit') : trans('promotion.breadcrumb.create'),
        href: '#',
    },
]);
</script>

<template>
    <Head :title="promotion ? 'Update Promotion' : 'Create Promotion'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <BackButton
                :href="route('promotions.index')"
                :label="promotion ? trans('promotion.title.update_promotion') : trans('promotion.title.create_promotion')"
            />
            <PromotionForm :promotion="promotion" :items="items" />
        </div>
    </AppLayout>
</template>
