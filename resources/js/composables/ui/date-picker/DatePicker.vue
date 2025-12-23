<script setup lang="ts">
import { type DateValue, getLocalTimeZone, parseDate } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { computed, ref, watch } from 'vue';

// Import your custom formatDate utility
import { formatDate } from '@/utils/formatDate';

// Define props and emits for v-model
interface Props {
    modelValue?: string | null;
    placeholder?: string;
    format?: 'short' | 'long' | 'table' | 'custom';
    useTranslations?: boolean;
}

interface Emits {
    (e: 'update:modelValue', value: string | null): void;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Pick a date',
    format: 'short',
    useTranslations: true,
});

const emit = defineEmits<Emits>();

const value = ref<DateValue>();

// Function to parse date string to DateValue
const parseStringToDateValue = (dateString: string | null): DateValue | undefined => {
    if (!dateString) return undefined;

    try {
        // Handle different date formats
        let cleanDateString = dateString;

        // If it's a datetime string, extract just the date part
        if (dateString.includes(' ')) {
            cleanDateString = dateString.split(' ')[0];
        }

        // If it's already in YYYY-MM-DD format
        if (/^\d{4}-\d{2}-\d{2}$/.test(cleanDateString)) {
            return parseDate(cleanDateString);
        }

        // If it's a full datetime or other format, try to parse it
        const date = new Date(dateString);
        if (!isNaN(date.getTime())) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return parseDate(`${year}-${month}-${day}`);
        }

        return undefined;
    } catch (error) {
        console.error('Error parsing date:', error, 'Input:', dateString);
        return undefined;
    }
};

// Watch for changes in modelValue prop
watch(
    () => props.modelValue,
    (newValue) => {
        value.value = parseStringToDateValue(newValue);
    },
    { immediate: true },
);

// Watch for changes in internal value
watch(value, (newValue) => {
    if (newValue) {
        // Convert DateValue to string format (YYYY-MM-DD)
        const dateString = newValue.toString();
        emit('update:modelValue', dateString);
    } else {
        emit('update:modelValue', null);
    }
});

// Use your custom formatDate utility for display
const displayValue = computed(() => {
    if (value.value) {
        const jsDate = value.value.toDate(getLocalTimeZone());

        // Use your custom formatDate utility
        return formatDate(jsDate, {
            format: props.format,
            useTranslations: props.useTranslations,
        });
    }
    return props.placeholder;
});
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button variant="outline" :class="cn('w-full justify-start text-left font-normal', !value && 'text-muted-foreground')">
                <CalendarIcon class="mr-2 h-4 w-4" />
                {{ displayValue }}
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <Calendar v-model="value" initial-focus />
        </PopoverContent>
    </Popover>
</template>
