<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8 dark:bg-gray-900">
        <div class="w-full max-w-md space-y-8 text-center">
            <div>
                <h1 class="text-9xl font-bold text-gray-300 dark:text-gray-600">403</h1>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-gray-100">Access Forbidden</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ message || 'You do not have permission to access this page.' }}
                </p>
            </div>

            <div class="mt-8 space-y-4">
                <button
                    @click="goBack"
                    class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition-colors duration-200 hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none dark:bg-indigo-500 dark:hover:bg-indigo-600 dark:focus:ring-offset-gray-900"
                >
                    Go Back
                </button>

                <Link
                    :href="route('dashboard')"
                    class="group relative flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-900"
                >
                    Go to Dashboard
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Props {
    status?: number;
    message?: string;
}

defineProps<Props>();

const goBack = () => {
    window.history.back();
};
</script>
