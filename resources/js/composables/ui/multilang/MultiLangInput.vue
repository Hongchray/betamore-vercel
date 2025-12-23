<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <!-- English input -->
        <div class="space-y-2">
            <Label for="name_en" class="text-sm font-medium"> {{ title }} (EN) <span class="text-red-500">*</span> </Label>
            <Input id="name_en" v-model="valueEn" type="text" :placeholder="`${title} (EN)`" :class="{ 'border-red-500': errorEn }" />
            <p v-if="errorEn" class="text-xs text-red-500">{{ errorEn }}</p>
        </div>

        <!-- Khmer input -->
        <div class="space-y-2">
            <Label for="name_km" class="text-sm font-medium"> {{ title }} (KM) <span class="text-red-500">*</span> </Label>
            <Input id="name_km" v-model="valueKm" type="text" :placeholder="`${title} (KM)`" :class="{ 'border-red-500': errorKm }" />
            <p v-if="errorKm" class="text-xs text-red-500">{{ errorKm }}</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { computed, inject } from 'vue';
const props = withDefaults(
    defineProps<{
        en: string;
        km: string;
        title?: string;
    }>(),
    {
        title: 'Name',
    },
);

const form = inject<Record<string, any>>('form');
if (!form) {
    console.warn('[MultiLangInput] No form provided via provide("form")');
}

// English binding
const valueEn = computed({
    get: () => getValueByPath(form, props.en),
    set: (val: string) => setValueByPath(form, props.en, val),
});
const errorEn = computed(() => form?.errors?.[props.en.split('.').pop()!]);

// Khmer binding
const valueKm = computed({
    get: () => getValueByPath(form, props.km),
    set: (val: string) => setValueByPath(form, props.km, val),
});
const errorKm = computed(() => form?.errors?.[props.km.split('.').pop()!]);

// Helpers for path-based access
function getValueByPath(obj: any, path: string): any {
    return path.split('.').reduce((o, i) => o?.[i], obj);
}

function setValueByPath(obj: any, path: string, value: any): void {
    const parts = path.split('.');
    const last = parts.pop()!;
    const target = parts.reduce((o, i) => o[i], obj);
    target[last] = value;
}

// Trans function fallback
const trans = window.trans || ((s: string) => s);
</script>
