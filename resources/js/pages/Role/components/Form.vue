<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Separator from '@/components/ui/separator/Separator.vue';
import { Textarea } from '@/components/ui/textarea';
import { useToast } from '@/components/ui/toast/use-toast';
import RequiredMark from '@/composables/ui/required-mark/RequiredMark.vue';
import { useForm } from '@inertiajs/vue3';
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
const { toast } = useToast();

// Form data
const form = useForm({
    name: props.role?.name || '',
    description: props.role?.description || '',
    permissions: [...(props.rolePermissions || [])],
});

const clearAllPermissions = () => {
    form.permissions.splice(0, form.permissions.length);
};

const hasAllPermissions = computed(() => {
    return props.permissions.length > 0 && form.permissions.length === props.permissions.length;
});

const hasNoPermissions = computed(() => {
    return form.permissions.length === 0;
});

const selectAllPermissions = () => {
    form.permissions.splice(0, form.permissions.length);
    if (Array.isArray(props.permissions)) {
        form.permissions.push(...props.permissions.map((p) => p.id));
    }
};

const submit = () => {
    const url = isEditMode.value ? route('roles.update', props.role!.id) : route('roles.store');
    const method = isEditMode.value ? 'put' : 'post';

    form[method](url, {
        onSuccess: () => {
            toast({
                variant: 'default',
                duration: 3000,
                description: isEditMode.value ? trans('role.messages.role_updated_successfully') : trans('role.messages.role_created_successfully'),
            });
        },
        onError: (error) => {
            toast({
                variant: 'destructive',
                duration: 3000,
                description: error?.message || trans('role.messages.role_create_failed'),
            });
        },
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-4">
        <Card>
            <CardHeader>
                <CardTitle>{{ trans('role.form.role_info') }}</CardTitle>
                <CardDescription>{{ trans('role.form.basic_info') }}</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="space-y-2">
                    <Label for="name">{{ trans('role.form.name') }} <RequiredMark /> </Label>
                    <Input id="name" v-model="form.name" :placeholder="trans('role.form.name')" :class="{ 'border-red-500': form.errors.name }" />
                    <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="description">{{ trans('role.form.description') }}</Label>
                    <Textarea id="description" v-model="form.description" rows="3" :class="{ 'border-red-500': form.errors.description }" />
                    <p v-if="form.errors.description" class="text-sm text-destructive">{{ form.errors.description }}</p>
                </div>
            </CardContent>
        </Card>
        <Separator />

        <Card>
            <CardHeader>
                <CardTitle class="flex items-center justify-between">
                    <span>{{ trans('role.form.permission') }}</span>
                    <div class="flex gap-2">
                        <Button type="button" variant="outline" size="sm" @click="selectAllPermissions" :disabled="hasAllPermissions">
                            {{ trans('role.button.select_all') }}
                        </Button>

                        <Button type="button" variant="outline" size="sm" @click="clearAllPermissions" :disabled="hasNoPermissions">
                            {{ trans('role.button.clear_all') }}
                        </Button>
                    </div>
                </CardTitle>
                <CardDescription>{{ trans('role.form.permission_title') }}</CardDescription>
            </CardHeader>

            <CardContent>
                <p v-if="form.errors.permissions" class="mb-4 text-sm text-destructive">
                    {{ form.errors.permissions }}
                </p>

                <div class="py-4">
                    <div class="rounded-lg border border-gray-200 bg-white p-6">
                        <PermissionHierarchy :permissions="permissions" v-model="form.permissions" />
                    </div>
                </div>
                <div class="flex justify-end gap-4">
                    <Button type="button" variant="outline" @click="$inertia.visit(route('roles.index'))">{{ trans('role.button.cancel') }}</Button>
                    <Button type="submit" :disabled="form.processing" class="min-w-[120px]">
                        <span v-if="form.processing">{{ isEditMode ? trans('role.button.updating') : trans('role.button.creating') }}</span>
                        <span v-else>{{ isEditMode ? trans('role.button.update') : trans('role.button.create') }}</span>
                    </Button>
                </div>
            </CardContent>
        </Card>
    </form>
</template>
