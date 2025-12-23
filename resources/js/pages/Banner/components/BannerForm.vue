<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useToast } from '@/components/ui/toast/use-toast';
import CancelButton from '@/composables/ui/cancel-button/CancelButton.vue';
import ImageUpload from '@/composables/ui/upload-image/UploadImage.vue';
import { useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { Loader2 } from 'lucide-vue-next';
import { computed, watch } from 'vue';
import type { Banner } from '../data/schema';

type FormData = Omit<Banner, 'id'>;

const props = defineProps<{
    banner?: Banner | null;
}>();

const isEditMode = computed(() => props.banner !== null && props.banner !== undefined);
const submitButtonText = computed(() => (isEditMode.value ? trans('banner.form.buttons.update') : trans('banner.form.buttons.create')));
const { toast } = useToast();

const form = useForm<Partial<FormData>>({
    name: props.banner?.name ?? '',
    banner_image: props.banner?.banner_image ?? '',
    description: props.banner?.description ?? '',
});

// Watch for changes in banner_image to debug
watch(
    () => form.banner_image,
    (newValue) => {
        console.log('Banner image changed:', newValue);
    },
);

const handleSubmit = () => {
    const url = isEditMode.value ? route('banners.update', props.banner!.id) : route('banners.store');
    const method = isEditMode.value ? 'put' : 'post';

    form[method](url, {
        onSuccess: () => {
            toast({
                variant: 'default',
                duration: 3000,
                description: isEditMode.value
                    ? trans('banner.messages.banner_updated_successfully')
                    : trans('banner.messages.banner_created_successfully'),
            });
        },
        onError: (errors) => {
            console.log('Form submission errors:', errors);
            toast({
                variant: 'destructive',
                duration: 3000,
                description: errors?.banner_image || errors?.message || trans('banner.messages.banner_create_failed'),
            });
        },
    });
};
</script>

<template>
    <Card class="w-full rounded-xl border p-4 shadow-sm sm:p-6 md:w-full xl:w-1/2">
        <CardHeader class="mb-4">
            <CardTitle class="text-xl font-semibold">{{
                isEditMode ? trans('banner.form.edit_description') : trans('banner.form.create_description')
            }}</CardTitle>
            <!-- <CardDescription class="text-sm text-muted-foreground">
                {{ isEditMode ? 'Update banner information' : 'Create a new banner' }}
            </CardDescription> -->
        </CardHeader>
        <CardContent>
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <div class="space-y-2">
                    <Label for="banner_image" class="text-sm font-medium">
                        {{ trans('banner.form.fields.banner_image') }} <span class="text-red-500">*</span>
                    </Label>
                    <ImageUpload
                        v-model="form.banner_image"
                        :class="{ 'border-red-500': form.errors.banner_image }"
                        type="banner"
                        @update:modelValue="
                            (value) => {
                                form.banner_image = value;
                                console.log('ImageUpload updated:', value);
                            }
                        "
                    />
                    <p v-if="form.errors.banner_image" class="text-xs text-red-500">{{ form.errors.banner_image }}</p>
                    <!-- Debug info -->
                    <!-- <p class="text-xs text-gray-500">Current value: {{ form.banner_image || 'empty' }}</p> -->
                </div>

                <div class="space-y-2">
                    <Label for="name" class="text-sm font-medium"> {{ trans('banner.form.fields.name') }} <span class="text-red-500">*</span> </Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        :placeholder="trans('banner.form.placeholders.name')"
                        :class="{ 'border-red-500': form.errors.name }"
                    />
                    <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="description" class="text-sm font-medium">
                        {{ trans('banner.form.fields.description') }}
                    </Label>
                    <Input
                        id="description"
                        v-model="form.description"
                        type="text"
                        :placeholder="trans('banner.form.placeholders.description')"
                        :class="{ 'border-red-500': form.errors.description }"
                    />
                    <p v-if="form.errors.description" class="text-xs text-red-500">{{ form.errors.description }}</p>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <CancelButton :fallback-route="route('items.index')" />

                    <Button type="submit" :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        {{ submitButtonText }}
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
