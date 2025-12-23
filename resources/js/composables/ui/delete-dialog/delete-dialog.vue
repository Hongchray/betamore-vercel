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
import { trans } from 'laravel-vue-i18n';
import { ref, watch } from 'vue';
const props = defineProps<{
    open: boolean;
    title: string;
    description: string;
    sumitButtonTitle?: string;
    withNote?: boolean;
    note?: string;
}>();
const noteContent = ref(props.note || '');

watch(
    () => props.note,
    (newVal) => {
        noteContent.value = newVal || '';
    },
);
const emit = defineEmits<{
    'update:open': [value: boolean];
    confirm: [];
    cancel: [];
}>();
</script>

<template>
    <AlertDialog :open="open" @update:open="(value) => emit('update:open', value)">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>{{ trans('composable.delete_dialog.title') }}</AlertDialogTitle>
                <AlertDialogDescription>{{ trans('composable.delete_dialog.description') }}</AlertDialogDescription>
            </AlertDialogHeader>
            <slot></slot>
            <AlertDialogFooter>
                <AlertDialogCancel @click="emit('cancel')"> {{ trans('composable.delete_dialog.cancel') }} </AlertDialogCancel>
                <AlertDialogAction @click="emit('confirm')" :disabled="withNote && (!noteContent || !noteContent.trim())">
                    <span v-if="sumitButtonTitle"> {{ sumitButtonTitle }}</span>
                    <span v-else> {{ trans('composable.delete_dialog.delete') }}</span>
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
