<script setup lang="ts">
import BackButton from '@/composables/ui/back-button/BackButton.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import Form from './components/BannerForm.vue';
import { Banner } from './data/schema';
interface ComboboxItem {
    value: string;
    label: string;
}
interface Props {
    banner?: Banner | null;
}

const props = defineProps<Props>();
const isEditMode = computed(() => props.banner !== null && props.banner !== undefined);

const breadcrumbs = computed((): BreadcrumbItem[] => {
    if (isEditMode.value && props.banner?.id) {
        return [
            {
                title: trans('banner.breadcrumb.index'),
                href: '/banners',
            },
            {
                title: trans('banner.breadcrumb.edit'),
                href: `/banners/${props.banner.id}/edit`,
            },
        ];
    }

    return [
        {
            title: trans('banner.breadcrumb.index'),
            href: '/banners',
        },
        {
            title: trans('banner.breadcrumb.create'),
            href: '/banners/create',
        },
    ];
});
</script>

<template>
    <Head :title="isEditMode ? 'Update Banner' : 'Create Banner'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <BackButton
                :href="route('banners.index')"
                :label="isEditMode ? trans('banner.title.update_banner') : trans('banner.title.create_banner')"
            />
            <Form :banner="banner" />
        </div>
    </AppLayout>
</template>
