<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { useToast } from '@/components/ui/toast/use-toast';
import CancelButton from '@/composables/ui/cancel-button/CancelButton.vue';
import MultiLangInput from '@/composables/ui/multilang/MultiLangInput.vue';
import MultiLangTextArea from '@/composables/ui/multilang/MultiLangTextArea.vue';
import ImageUpload from '@/composables/ui/upload-image/UploadImage.vue';
import { useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { Loader2 } from 'lucide-vue-next';
import { computed, provide } from 'vue';
import type { Company } from '../data/schema';
type FormData = Omit<Company, 'id'>;

const props = defineProps<{
    company?: Company | null;
}>();

const isEditMode = computed(() => props.company !== null && props.company !== undefined);
const submitButtonText = computed(() => (isEditMode.value ? trans('company.form.buttons.update') : trans('company.form.buttons.create')));
const { toast } = useToast();

const form = useForm<Partial<FormData>>({
    name_en: props.company?.name_en ?? '',
    name_km: props.company?.name_km ?? '',
    logo: props.company?.logo ?? '',
    description_en: props.company?.description_en ?? '',
    description_km: props.company?.description_km ?? '',
});

const handleSubmit = () => {
    const url = isEditMode.value ? route('companies.update', props.company!.id) : route('companies.store');
    const method = isEditMode.value ? 'put' : 'post';

    form[method](url, {
        onSuccess: () => {
            toast({
                variant: 'default',
                duration: 3000,
                description: isEditMode.value ? trans('company.messages.updated_successfully') : trans('company.messages.created_successfully'),
            });
        },
        onError: (errors) => {
            console.log('Form submission failed with errors:', errors); // 👈 Logs the errors from the backend

            toast({
                variant: 'destructive',
                duration: 3000,
                description: errors?.logo || errors?.message || trans('company.messages.create_failed'),
            });
        },
    });
};
provide('form', form);
</script>

<template>
    <Card class="w-full rounded-xl border p-6 shadow-sm xl:w-1/2">
        <CardHeader class="mb-4">
            <CardTitle class="text-xl font-semibold">
                {{ isEditMode ? trans('company.form.edit_description') : trans('company.form.create_description') }}
            </CardTitle>
            <!-- <CardDescription class="text-sm text-muted-foreground">
                {{ isEditMode ? 'Update company information' : 'Create a new company' }}
            </CardDescription> -->
        </CardHeader>

        <CardContent>
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <div class="space-y-2">
                    <Label for="logo" class="text-sm font-medium">
                        {{ trans('company.form.fields.logo') }}
                    </Label>
                    <ImageUpload
                        v-model="form.logo"
                        :class="{ 'border-red-500': form.errors.logo }"
                        @update:modelValue="(value) => (form.logo = value)"
                    />
                    <p v-if="form.errors.logo" class="text-xs text-red-500">{{ form.errors.logo }}</p>
                </div>

                <MultiLangInput en="name_en" km="name_km" :title="trans('company.form.fields.name')" />

                <MultiLangTextArea en="description_en" km="description_km" :title="trans('company.form.fields.description')" />

                <div class="flex items-center justify-end gap-4">
                    <CancelButton :fallback-route="route('companies.index')" />
                    <Button type="submit" :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        {{ submitButtonText }}
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
