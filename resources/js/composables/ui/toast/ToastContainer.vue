<script setup lang="ts">
import { useToast } from '@/composables/ui/toast/useToast';
import Toast from './Toast.vue';

const { toasts, removeToast } = useToast();
</script>

<template>
    <Teleport to="body">
        <div class="pointer-events-none fixed right-4 bottom-4 z-[9999]">
            <div class="pointer-events-auto">
                <Toast
                    v-for="toast in toasts"
                    :key="toast.id"
                    :type="toast.type"
                    :title="toast.title"
                    :description="toast.description"
                    @close="removeToast(toast.id)"
                />
            </div>
        </div>
    </Teleport>
</template>
