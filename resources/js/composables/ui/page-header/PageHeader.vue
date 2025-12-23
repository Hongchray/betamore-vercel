<script setup lang="ts">
interface PageHeaderProps {
    title: string;
    description?: string;
}

defineProps<PageHeaderProps>();
</script>

<template>
    <div class="space-y-2">
        <h1 class="text-2xl font-bold tracking-tight">{{ title }}</h1>
        <p v-if="description" class="text-muted-foreground">{{ description }}</p>
        <div v-if="$slots.actions" class="flex items-center gap-2 pt-2">
            <slot name="actions" />
        </div>
    </div>
</template>
