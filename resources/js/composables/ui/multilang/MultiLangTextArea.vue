<template>
    <div class="space-y-4">
        <!-- English Textarea -->
        <div class="space-y-2">
            <Label for="textarea_en" class="text-sm font-medium"> {{ title }} (EN) <span class="text-red-500"></span> </Label>
            <Textarea id="textarea_en" v-model="valueEn" :placeholder="`${title} (EN)`" rows="4" :class="{ 'border-red-500': errorEn }" />
            <p v-if="errorEn" class="text-xs text-red-500">{{ errorEn }}</p>
        </div>

        <!-- Khmer Textarea -->
        <div class="space-y-2">
            <Label for="textarea_km" class="text-sm font-medium"> {{ title }} (KM) <span class="text-red-500"></span> </Label>
            <Textarea id="textarea_km" v-model="valueKm" :placeholder="`${title} (KM)`" rows="4" :class="{ 'border-red-500': errorKm }" />
            <p v-if="errorKm" class="text-xs text-red-500">{{ errorKm }}</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Textarea } from '@/components/ui/textarea'; // Adjust if needed
import { computed, inject } from 'vue';

const props = withDefaults(
    defineProps<{
        en: string;
        km: string;
        title?: string;
    }>(),
    {
        title: 'Description',
    },
);

const form = inject<Record<string, any>>('form');
if (!form) {
    console.warn('[MultiLangTextArea] No form provided via provide("form")');
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

// Trans fallback
const trans = window.trans || ((s: string) => s);
</script>
