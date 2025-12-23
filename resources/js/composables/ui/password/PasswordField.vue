<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Eye, EyeOff } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    modelValue: string;
    error?: string;
    label?: string;
    placeholder?: string;
    helpText?: string;
    required?: boolean;
    id?: string;
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Password',
    placeholder: 'Enter password',
    required: true,
    id: 'password',
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const showPassword = ref(false);
const inputType = computed(() => (showPassword.value ? 'text' : 'password'));

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <div class="space-y-2">
        <Label :for="id">
            {{ label }}
            <span v-if="helpText" class="ml-1 text-sm text-muted-foreground">{{ helpText }}</span>
        </Label>
        <div class="relative">
            <Input
                :id="id"
                :model-value="modelValue"
                :type="inputType"
                :placeholder="placeholder"
                :class="{ 'border-red-500': error }"
                @update:model-value="emit('update:modelValue', $event)"
            />
            <Button type="button" variant="ghost" size="sm" class="absolute top-0 right-0 h-full px-3 hover:bg-transparent" @click="togglePassword">
                <Eye v-if="!showPassword" class="h-4 w-4" />
                <EyeOff v-else class="h-4 w-4" />
            </Button>
        </div>
        <p v-if="error" class="text-xs text-red-500">{{ error }}</p>
    </div>
</template>
