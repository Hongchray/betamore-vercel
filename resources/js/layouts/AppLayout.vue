<script setup lang="ts">
import { ToastAction, Toaster, useToast } from '@/components/ui/toast';
import PermissionDialog from '@/composables/ui/permission-dialog/PermissionDialog.vue';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { cn } from '@/lib/utils';
import type { BreadcrumbItemType } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { h } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

const { toast } = useToast();
const page = usePage();

const showToast = () => {
    toast({
        variant: 'default',
        duration: 9000,
        class: cn('fixed top-0 right-0 flex md:top-4 md:right-4 md:max-w-[420px]'),
        title: 'New order has been created',
        description: 'please check it out.',
        action: h(
            ToastAction,
            {
                altText: 'Check',
                onClick: () => {
                    handleClick();
                },
            },
            {
                default: () => 'Check',
            },
        ),
    });
};

const handleClick = () => {
    router.get(route('orders.index'));
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <div class="flex h-screen overflow-hidden">
        <AppLayout :breadcrumbs="breadcrumbs" class="min-w-0 flex-1">
            <Toaster />
            <PermissionDialog />

            <div class="w-full min-w-0">
                <slot />
            </div>
        </AppLayout>
    </div>
</template>
