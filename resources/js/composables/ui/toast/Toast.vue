<script setup lang="ts">
import { AlertCircle, CheckCircle, Info, X, XCircle } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    type?: 'success' | 'error' | 'warning' | 'info';
    title: string;
    description?: string;
    onClose: () => void;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'info',
});

const iconComponent = computed(() => {
    switch (props.type) {
        case 'success':
            return CheckCircle;
        case 'error':
            return XCircle;
        case 'warning':
            return AlertCircle;
        default:
            return Info;
    }
});

const colorClasses = computed(() => {
    switch (props.type) {
        case 'success':
            return 'border-l-green-500 bg-green-50 text-green-800';
        case 'error':
            return 'border-l-red-500 bg-red-50 text-red-800';
        case 'warning':
            return 'border-l-yellow-500 bg-yellow-50 text-yellow-800';
        default:
            return 'border-l-blue-500 bg-blue-50 text-blue-800';
    }
});

const iconColor = computed(() => {
    switch (props.type) {
        case 'success':
            return 'text-green-500';
        case 'error':
            return 'text-red-500';
        case 'warning':
            return 'text-yellow-500';
        default:
            return 'text-blue-500';
    }
});
</script>

<template>
    <div class="toast-item animate-slide-in mb-3 flex max-w-sm items-start gap-3 rounded-lg border-l-4 bg-white p-4 shadow-lg" :class="colorClasses">
        <component :is="iconComponent" :class="iconColor" class="mt-0.5 h-5 w-5 flex-shrink-0" />

        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium">{{ title }}</p>
            <p v-if="description" class="mt-1 text-sm opacity-90">{{ description }}</p>
        </div>

        <button @click="onClose" class="flex-shrink-0 rounded p-1 transition-colors hover:bg-black/10">
            <X class="h-4 w-4" />
        </button>
    </div>
</template>

<style scoped>
.animate-slide-in {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast-item {
    transition: all 0.3s ease;
}
</style>
