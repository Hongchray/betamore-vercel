<template>
    <div class="flex items-center gap-3">
        <Button variant="outline" class="w-10 items-center font-bold" @click="navigateBack">
            <ChevronLeftIcon class="h-6 w-6" />
        </Button>
        <h1 class="text-xl font-bold tracking-tight">{{ label }}</h1>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/vue3';
import { ChevronLeftIcon } from 'lucide-vue-next';
import { defineProps } from 'vue';

const props = defineProps({
    label: {
        type: String,
        default: 'Back',
    },
    href: {
        type: String,
        required: false,
        default: '',
    },
});

const navigateBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else if (props.href) {
        router.visit(props.href);
    } else {
        console.warn('No previous history and no fallback href provided.');
    }
};
</script>
