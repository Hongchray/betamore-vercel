<template>
    <Popover v-model:open="isOpen">
        <PopoverTrigger as-child>
            <Input
                :model-value="formattedValue"
                readonly
                class="w-full cursor-pointer"
                placeholder="hh:mm AM/PM"
                @click="isOpen = true"
                :disabled="disabled"
            />
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0" align="start">
            <div class="flex gap-2 p-2">
                <Select v-model="localHour">
                    <SelectTrigger class="w-[70px]">
                        <SelectValue>{{ localHour }}</SelectValue>
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="hour in hours" :key="hour" :value="hour">
                            {{ hour }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <span class="text-2xl">:</span>
                <Select v-model="localMinute">
                    <SelectTrigger class="w-[70px]">
                        <SelectValue>{{ localMinute }}</SelectValue>
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="minute in minutes" :key="minute" :value="minute">
                            {{ minute }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <Select v-model="localPeriod" class="">
                    <SelectTrigger class="w-[70px]">
                        <SelectValue>{{ localPeriod }}</SelectValue>
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="AM">AM</SelectItem>
                        <SelectItem value="PM">PM</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="flex justify-between bg-gray-100 p-2">
                <Button variant="outline" size="sm" @click="handleNow">Now</Button>
                <Button @click="handleSelect">OK</Button>
            </div>
        </PopoverContent>
    </Popover>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    modelValue: string;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const isOpen = ref(false);
const localHour = ref('12');
const localMinute = ref('00');
const localPeriod = ref('AM');

const hours = computed(() => Array.from({ length: 12 }, (_, i) => (i + 1).toString().padStart(2, '0')));
const minutes = computed(() => Array.from({ length: 60 }, (_, i) => i.toString().padStart(2, '0')));

const formattedValue = computed(() => {
    if (!props.modelValue) return '';
    const [hour, minute] = props.modelValue.split(':');
    const hourNum = parseInt(hour, 10);
    const period = hourNum >= 12 ? 'PM' : 'AM';
    const hour12 = hourNum % 12 || 12;
    return `${hour12.toString().padStart(2, '0')}:${minute} ${period}`;
});

const handleSelect = () => {
    let hour24 = parseInt(localHour.value, 10);
    if (localPeriod.value === 'PM' && hour24 !== 12) {
        hour24 += 12;
    } else if (localPeriod.value === 'AM' && hour24 === 12) {
        hour24 = 0;
    }
    emit('update:modelValue', `${hour24.toString().padStart(2, '0')}:${localMinute.value}`);
    isOpen.value = false;
};

const handleNow = () => {
    const now = new Date();
    const hour = now.getHours();
    const minute = now.getMinutes();
    localHour.value = (hour % 12 || 12).toString().padStart(2, '0');
    localMinute.value = minute.toString().padStart(2, '0');
    localPeriod.value = hour >= 12 ? 'PM' : 'AM';
    handleSelect();
};

watch(
    () => props.modelValue,
    (newValue) => {
        if (newValue) {
            const [hour, minute] = newValue.split(':');
            const hourNum = parseInt(hour, 10);
            localHour.value = (hourNum % 12 || 12).toString().padStart(2, '0');
            localMinute.value = minute;
            localPeriod.value = hourNum >= 12 ? 'PM' : 'AM';
        }
    },
    { immediate: true },
);
</script>
