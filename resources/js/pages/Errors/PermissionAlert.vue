<template>
    <div class="fixed inset-0 z-50">
        <AlertDialog :open="true">
            <AlertDialogContent class="sm:max-w-[425px]">
                <AlertDialogHeader>
                    <AlertDialogTitle class="flex items-center gap-2 text-red-600 dark:text-red-400">
                        <ShieldX class="h-5 w-5" />
                        {{ trans('permissions.access_forbidden') }}
                    </AlertDialogTitle>
                    <AlertDialogDescription class="text-gray-600 dark:text-gray-400">
                        <div class="mt-2 rounded-md border-l-4 border-yellow-400 bg-yellow-50 p-4 dark:border-yellow-500 dark:bg-yellow-900/20">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <AlertTriangle class="h-5 w-5 text-yellow-400 dark:text-yellow-500" />
                                </div>
                                <div class="ml-3">
                                    <!-- Main message -->
                                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                        {{ message }}
                                    </p>
                                    <!-- Optional submessage -->
                                    <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                        <!-- {{ submessage }} -->
                                    </p>
                                </div>
                            </div>
                        </div>
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter class="gap-2">
                    <AlertDialogCancel @click="goBack">
                        {{ trans('permissions.go_back') }}
                    </AlertDialogCancel>
                    <AlertDialogAction @click="goToDashboard">
                        {{ trans('permissions.go_to_dashboard') }}
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
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
import { router } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { ShieldX } from 'lucide-vue-next';

interface Props {
    message: string;
    submessage: string;
}

defineProps<Props>();

const goBack = () => {
    window.history.back();
};

const goToDashboard = () => {
    router.visit(route('dashboard'));
};
</script>
