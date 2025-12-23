<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import Separator from '@/components/ui/separator/Separator.vue';
import RequiredMark from '@/composables/ui/required-mark/RequiredMark.vue';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import { type Permission, type Role } from '../data/schema';
import PermissionHierarchy from './PermissionHierarchy.vue';

interface Props {
    role?: Role | null;
    permissions: Permission[];
    rolePermissions: number[];
}

const props = defineProps<Props>();
const isEditMode = computed(() => props.role !== null && props.role !== undefined);
</script>

<template>
    <div class="space-y-4">
        <Card>
            <CardHeader>
                <CardTitle>{{ trans('role.form.role_info') }}</CardTitle>
                <CardDescription>{{ trans('role.form.basic_info') }}</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="space-y-2">
                    <Label for="name">{{ trans('role.form.name') }} <RequiredMark /></Label>
                    <p class="text-sm text-gray-900">{{ props.role?.name || '-' }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="description">{{ trans('role.form.description') }}</Label>
                    <p class="text-sm whitespace-pre-line text-gray-700">{{ props.role?.description || '-' }}</p>
                </div>
            </CardContent>
        </Card>

        <Separator />

        <Card>
            <CardHeader>
                <CardTitle class="flex items-center justify-between">
                    <span>{{ trans('role.form.permission') }}</span>
                </CardTitle>
                <CardDescription>{{ trans('role.form.permission_title') }}</CardDescription>
            </CardHeader>

            <CardContent>
                <div class="py-4">
                    <div class="rounded-lg border border-gray-200 bg-white p-6">
                        <PermissionHierarchy :permissions="permissions" :model-value="rolePermissions" readonly />
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
