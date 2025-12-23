<template>
    <Button type="button" variant="outline" @click="cancel">
        {{ label ? label : trans('composable.button.cancel') }}
    </Button>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n'; // ✅ import this
import { defineProps } from 'vue';

const props = defineProps({
    fallbackRoute: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        default: '',
    },
});

const cancel = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        router.visit(props.fallbackRoute);
    }
};
</script>
