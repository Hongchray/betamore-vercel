<script setup lang="ts">
import BackButton from '@/composables/ui/back-button/BackButton.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Form from '@/pages/Role/components/Form.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import { type Permission, type Role } from './data/schema';
interface Props {
    role?: Role | null;
    permissions?: Permission[];
    rolePermissions?: number[];
}

const props = defineProps<Props>();
const isEditMode = computed(() => props.role !== null && props.role !== undefined);

const breadcrumbs = computed((): BreadcrumbItem[] => {
    if (isEditMode.value && props.role?.id) {
        return [
            {
                title: trans('role.bread_crumb.role'),
                href: '/roles',
            },
            {
                title: trans('role.bread_crumb.role_update'),
                href: `/roles/${props.role.id}/edit`,
            },
        ];
    }

    return [
        {
            title: trans('role.bread_crumb.role'),
            href: '/roles',
        },
        {
            title: trans('role.bread_crumb.role_create'),
            href: `/roles/create`,
        },
    ];
});

const pageTitle = computed(() => (isEditMode.value ? 'Edit Role Permission' : 'Create Role Permission'));

const pageDescription = computed(() =>
    isEditMode.value ? `Update ${props.role?.name}'s information and permissions.` : 'Create a new role with appropriate permissions.',
);
</script>

<template>
    <Head :title="pageTitle" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <BackButton :href="route('roles.index')" :label="isEditMode ? trans('role.title.update_role') : trans('role.title.create_role')" />
            <Form :role="role" :permissions="permissions || []" :role-permissions="rolePermissions || []" />
        </div>
    </AppLayout>
</template>
