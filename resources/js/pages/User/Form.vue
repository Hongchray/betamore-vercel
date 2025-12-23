<script setup lang="ts">
import BackButton from '@/composables/ui/back-button/BackButton.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import Form from './components/UserForm.vue';
import { User } from './data/schema';
interface ComboboxItem {
    value: string;
    label: string;
}
interface Props {
    user?: User | null;
    user_roles: ComboboxItem[];
}

const props = defineProps<Props>();
const isEditMode = computed(() => props.user !== null && props.user !== undefined);

const breadcrumbs = computed((): BreadcrumbItem[] => {
    if (isEditMode.value && props.user?.id) {
        return [
            {
                title: trans('user.breadcrumb.index'),
                href: '/users',
            },
            {
                title: trans('user.breadcrumb.edit'),
                href: `/users/${props.user.id}/edit`,
            },
        ];
    }

    return [
        {
            title: trans('user.breadcrumb.index'),
            href: '/users',
        },
        {
            title: trans('user.breadcrumb.create'),
            href: '/users/create',
        },
    ];
});
</script>

<template>
    <Head :title="isEditMode ? 'Edit User' : 'Create User'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <BackButton :href="route('users.index')" :label="isEditMode ? trans('user.title.update_user') : trans('user.title.create_user')" />
            <Form :user="user" :user_roles="user_roles" />
        </div>
    </AppLayout>
</template>
