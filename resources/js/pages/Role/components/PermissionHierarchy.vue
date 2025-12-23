<template>
    <div class="grid grid-cols-4 gap-6">
        <div v-for="(categoryPermissions, category) in groupedPermissions" :key="category" class="space-y-2">
            <div class="flex items-center space-x-2">
                <Checkbox
                    :id="`category-${category}`"
                    :modelValue="isCategorySelected(category)"
                    :indeterminate="isCategoryIndeterminate(category)"
                    :disabled="props.readonly"
                    @update:modelValue="(checked) => toggleCategory(category, checked)"
                />
                <Label :for="`category-${category}`" class="cursor-pointer font-medium text-gray-900 capitalize">
                    {{ category }}
                </Label>
            </div>

            <div class="ml-6 space-y-2">
                <div v-for="permission in categoryPermissions" :key="permission.id" class="flex items-center space-x-2">
                    <Checkbox
                        :id="`permission-${permission.id}`"
                        :modelValue="form.permissions.includes(permission.id)"
                        :disabled="props.readonly"
                        @update:modelValue="(checked) => togglePermission(permission.id, checked)"
                    />
                    <Label :for="`permission-${permission.id}`" class="cursor-pointer text-gray-700">
                        {{ formatPermissionName(permission.name) }}
                    </Label>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import Label from '@/components/ui/label/Label.vue';
import { computed, reactive } from 'vue';
import { type Permission } from '../data/schema';

interface FormData {
    permissions: string[];
}

interface Props {
    permissions: Permission[];
    modelValue: string[];
    readonly?: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:modelValue': [value: string[]];
}>();

const form = reactive<FormData>({
    permissions: [...props.modelValue],
});

const updateModelValue = () => {
    emit('update:modelValue', form.permissions);
};

const groupedPermissions = computed(() => {
    const groups: Record<string, Permission[]> = {};

    props.permissions.forEach((permission) => {
        const category = permission.name.split('.')[0];
        if (!groups[category]) {
            groups[category] = [];
        }
        groups[category].push(permission);
    });

    const sorted: Record<string, Permission[]> = {};

    Object.entries(groups)
        .sort(([aKey, aPermissions], [bKey, bPermissions]) => {
            const aLen = aPermissions.length;
            const bLen = bPermissions.length;

            if (aLen !== bLen) {
                return bLen - aLen;
            }

            return aKey.localeCompare(bKey);
        })
        .forEach(([key, value]) => {
            sorted[key] = value;
        });

    return sorted;
});

const formatPermissionName = (permissionName: string): string => {
    const parts = permissionName.split('.');
    return parts[1] ? parts[1].charAt(0).toUpperCase() + parts[1].slice(1) : permissionName;
};

const isCategorySelected = (category: string): boolean => {
    const categoryPermissions = groupedPermissions.value[category];
    return categoryPermissions.every((permission) => form.permissions.includes(permission.id));
};

const isCategoryIndeterminate = (category: string): boolean => {
    const categoryPermissions = groupedPermissions.value[category];
    const selectedCount = categoryPermissions.filter((permission) => form.permissions.includes(permission.id)).length;
    return selectedCount > 0 && selectedCount < categoryPermissions.length;
};

const toggleCategory = (category: string, checked: boolean) => {
    if (props.readonly) return;

    const categoryPermissions = groupedPermissions.value[category];
    if (checked) {
        categoryPermissions.forEach((permission) => {
            if (!form.permissions.includes(permission.id)) {
                form.permissions.push(permission.id);
            }
        });
    } else {
        categoryPermissions.forEach((permission) => {
            const index = form.permissions.indexOf(permission.id);
            if (index > -1) {
                form.permissions.splice(index, 1);
            }
        });
    }
    updateModelValue();
};

const togglePermission = (permissionId: string, checked: boolean) => {
    if (props.readonly) return;

    if (checked) {
        if (!form.permissions.includes(permissionId)) {
            form.permissions.push(permissionId);
        }
    } else {
        const index = form.permissions.indexOf(permissionId);
        if (index > -1) {
            form.permissions.splice(index, 1);
        }
    }
    updateModelValue();
};
</script>
