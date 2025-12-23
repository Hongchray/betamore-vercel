<script lang="ts" setup>
import { DiscountType, formatDiscount } from '@/utils/formatDiscount';
import { defineProps } from 'vue';

interface Props {
    value: number;
    type: DiscountType;
}

const props = defineProps<Props>();
</script>

<template>
    <div class="text-left font-medium">
        {{ formatDiscount({ value: props.value, type: props.type }) }}
    </div>
</template>
