<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Switch from '@/components/ui/switch/Switch.vue';
import { useToast } from '@/components/ui/toast/use-toast';
import CancelButton from '@/composables/ui/cancel-button/CancelButton.vue';
import RequiredMark from '@/composables/ui/required-mark/RequiredMark.vue';
import ImageUpload from '@/composables/ui/upload-image/UploadImage.vue';
import { useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { Loader2 } from 'lucide-vue-next';
import { computed } from 'vue';
import type { Delivery } from '../data/schema'; // You need to define this schema
type FormData = Omit<Delivery, 'id'>;

const props = defineProps<{
    delivery?: Delivery | null;
}>();

const isEditMode = computed(() => props.delivery !== null && props.delivery !== undefined);
const submitButtonText = computed(() => (isEditMode.value ? trans('delivery.button.update') : trans('delivery.button.create')));
const { toast } = useToast();

const form = useForm<Partial<FormData>>({
    name: props.delivery?.name ?? '',
    image: props.delivery?.image ?? '',
    shipping_fee: props.delivery?.shipping_fee ?? 0,
    description: props.delivery?.description ?? '',
    is_active: props.delivery?.is_active ?? true, // Default to active (1)
});

const handleSubmit = () => {
    const url = isEditMode.value ? route('deliveries.update', props.delivery!.id) : route('deliveries.store');
    const method = isEditMode.value ? 'put' : 'post';

    form[method](url, {
        onSuccess: () => {
            toast({
                variant: 'default',
                duration: 3000,
                description: isEditMode.value ? trans('delivery.messages.updated_successfully') : trans('delivery.messages.created_successfully'),
            });
        },
        onError: (error) => {
            toast({
                variant: 'destructive',
                duration: 3000,
                description: error?.message || trans('delivery.messages.create_failed'),
            });
        },
    });
};
</script>
<template>
    <Card class="w-full rounded-xl border p-6 shadow-sm xl:w-1/2">
        <CardHeader class="mb-4">
            <CardTitle class="text-xl font-semibold">
                {{ isEditMode ? trans('delivery.form.edit_description') : trans('delivery.form.create_description') }}
            </CardTitle>
            <CardDescription />
        </CardHeader>

        <CardContent>
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <ImageUpload v-model="form.image" />

                <div class="space-y-2">
                    <Label for="name">{{ trans('delivery.form.fields.name') }} <RequiredMark /></Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        :placeholder="trans('delivery.form.placeholders.name')"
                        :class="{ 'border-red-500': form.errors.name }"
                    />
                    <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="shipping_fee">{{ trans('delivery.form.fields.shipping_fee') }}<RequiredMark /></Label>
                    <Input
                        id="shipping_fee"
                        v-model="form.shipping_fee"
                        type="number"
                        step="any"
                        :placeholder="trans('delivery.form.placeholders.shipping_fee')"
                        :class="{ 'border-red-500': form.errors.shipping_fee }"
                    />
                    <p v-if="form.errors.shipping_fee" class="text-xs text-red-500">{{ form.errors.shipping_fee }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="description">{{ trans('delivery.form.fields.description') }}</Label>
                    <Input
                        id="description"
                        v-model="form.description"
                        type="text"
                        :placeholder="trans('delivery.form.placeholders.description')"
                        :class="{ 'border-red-500': form.errors.description }"
                    />
                    <p v-if="form.errors.description" class="text-xs text-red-500">{{ form.errors.description }}</p>
                </div>

                <!-- Active/Inactive Switch -->
                <div class="space-y-2">
                    <div class="flex items-center justify-start gap-4">
                        <div class="space-y-0.5">
                            <Label for="is_active">{{ trans('delivery.form.fields.is_active') }}</Label>
                        </div>
                        <Switch id="is_active" v-model="form.is_active" :class="{ 'border-red-500': form.errors.is_active }" />
                    </div>
                    <p v-if="form.errors.is_active" class="text-xs text-red-500">{{ form.errors.is_active }}</p>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <CancelButton :fallback-route="route('deliveries.index')" />
                    <Button type="submit" :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        {{ submitButtonText }}
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
