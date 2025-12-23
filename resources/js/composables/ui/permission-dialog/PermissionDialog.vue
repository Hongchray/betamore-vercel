<template>
    <AlertDialog :open="isOpen" @update:open="setIsOpen">
        <AlertDialogContent class="sm:max-w-[425px]">
            <AlertDialogHeader>
                <AlertDialogTitle class="flex items-center gap-2 text-red-600 dark:text-red-400">
                    <ShieldX class="h-5 w-5" />
                    Access Forbidden
                </AlertDialogTitle>
                <AlertDialogDescription class="text-gray-600 dark:text-gray-400">
                    {{ message }}
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter class="gap-2">
                <AlertDialogCancel @click="goBack"> Go Back </AlertDialogCancel>
                <AlertDialogAction @click="goToDashboard"> Go to Dashboard </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>

<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { router, usePage } from '@inertiajs/vue3';
import { ShieldX } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

const page = usePage();
const isOpen = ref(false);
const message = ref('');

// Debug: Log page props on mount
onMounted(() => {
    // Check if there's already a permission error
    if (page.props.flash?.permission_error) {
        console.log('Found existing permission error:', page.props.flash.permission_error);
        message.value = page.props.flash.permission_error;
        isOpen.value = true;
    }
});

// Watch for permission errors from the backend
watch(
    () => page.props.flash?.permission_error,
    (error) => {
        if (error) {
            message.value = error;
            isOpen.value = true;
            console.log('Dialog should open now');
        }
    },
    { immediate: true },
);

const setIsOpen = (open: boolean) => {
    console.log('Setting dialog open:', open);
    isOpen.value = open;
};

const goBack = () => {
    console.log('Going back');
    window.history.back();
    isOpen.value = false;
};

const goToDashboard = () => {
    console.log('Going to dashboard');
    router.visit(route('dashboard'));
    isOpen.value = false;
};
</script>
