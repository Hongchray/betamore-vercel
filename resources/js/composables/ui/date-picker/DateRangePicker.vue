<script setup lang="ts">
import { Button } from '@/components/ui/button';

import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { cn } from '@/lib/utils';
import { formatDateTable } from '@/utils/formatDate';
import { CalendarDate, getLocalTimeZone } from '@internationalized/date';
import { trans } from 'laravel-vue-i18n';
import { Calendar as CalendarIcon } from 'lucide-vue-next';
import { computed, type Ref, ref, watch } from 'vue';

const emit = defineEmits<{
    'update:dateRange': [value: { start: CalendarDate; end: CalendarDate }];
}>();

const today = computed(() => {
    const now = new Date();
    return new CalendarDate(now.getFullYear(), now.getMonth() + 1, now.getDate());
});

const value = ref({
    start: today.value.subtract({ days: 30 }),
    end: today.value,
}) as Ref<{ start: CalendarDate; end: CalendarDate }>;

const props = defineProps<{
    size?: 'sm' | 'lg' | 'default' | 'icon';
}>();

const presets = computed(() => {
    const current = today.value;
    return [
        {
            label: trans('composable.date_range_picker.today'),
            dates: { start: current, end: current },
        },
        {
            label: trans('composable.date_range_picker.last_7_days'),
            dates: { start: current.subtract({ days: 7 }), end: current },
        },
        {
            label: trans('composable.date_range_picker.last_30_days'),
            dates: { start: current.subtract({ days: 30 }), end: current },
        },
        {
            label: trans('composable.date_range_picker.last_90_days'),
            dates: { start: current.subtract({ days: 90 }), end: current },
        },
    ];
});

// Preset selector
const selectPreset = (preset: (typeof presets.value)[0]) => {
    value.value = preset.dates;
    emit('update:dateRange', preset.dates);
};

// Optional: Date formatter
const formatCalendarDate = (calendarDate: CalendarDate): string => {
    const jsDate = calendarDate.toDate(getLocalTimeZone());
    return formatDateTable(jsDate);
};

// Emit changes
watch(
    value,
    (newValue) => {
        if (newValue.start && newValue.end) {
            emit('update:dateRange', newValue);
        }
    },
    { deep: true },
);
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                :size="props.size ?? 'lg'"
                :class="cn('w-auto justify-start text-left font-normal', !value && 'text-muted-foreground')"
            >
                <CalendarIcon class="mr-2 h-4 w-4" />
                <template v-if="value.start">
                    <template v-if="value.end"> {{ formatCalendarDate(value.start) }} - {{ formatCalendarDate(value.end) }} </template>

                    <template v-else>
                        {{ formatCalendarDate(value.start) }}
                    </template>
                </template>
                <template v-else> {{ trans('composable.date_range_picker.pick_date') }} </template>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0" align="start">
            <div class="flex">
                <div class="flex flex-col sm:flex-row">
                    <RangeCalendar
                        v-model="value"
                        initial-focus
                        :number-of-months="2"
                        @update:start-value="(startDate) => (value.start = startDate)"
                    />
                </div>
                <div class="flex flex-col gap-2 border-l border-border p-4">
                    <div class="text-sm font-medium">{{ trans('composable.date_range_picker.quick_select') }}</div>
                    <div class="grid gap-1">
                        <Button
                            v-for="preset in presets"
                            :key="preset.label"
                            variant="ghost"
                            size="sm"
                            class="justify-start font-normal"
                            @click="selectPreset(preset)"
                        >
                            {{ preset.label }}
                        </Button>
                    </div>
                </div>
            </div>
        </PopoverContent>
    </Popover>
</template>
