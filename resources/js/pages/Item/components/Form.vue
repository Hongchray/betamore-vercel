<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useToast } from '@/components/ui/toast/use-toast';
import CancelButton from '@/composables/ui/cancel-button/CancelButton.vue';
import Combobox from '@/composables/ui/combobox/Combobox.vue';
import MultiLangInput from '@/composables/ui/multilang/MultiLangInput.vue';
import MultiLangTextArea from '@/composables/ui/multilang/MultiLangTextArea.vue';
import RequiredMark from '@/composables/ui/required-mark/RequiredMark.vue';
import UploadImage from '@/composables/ui/upload-image/UploadImage.vue';
import { useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { Plus, Trash2 } from 'lucide-vue-next';
import { computed, provide, ref, watch } from 'vue';
import { type Item } from '../data/schema';

interface ComboboxItem {
    value: string;
    label: string;
}

const unitValues = ['piece', 'box', 'kg', 'g', 'l', 'ml', 'pack', 'm', 'bottle', 'can', 'set', 'pair', 'sheet', 'carton'];

const unitOptions = computed(() =>
    unitValues.map((unit) => ({
        label: trans(`item.units.${unit}`),
        value: unit,
    })),
);
const props = defineProps<{
    item?: Item | null;
    companies: ComboboxItem[];
}>();

const isEditMode = computed(() => props.item !== null && props.item !== undefined);
const { toast } = useToast();
const mainImageIndex = ref<number | null>(null);

const form = useForm({
    name_en: props.item?.name_en,
    name_km: props.item?.name_km,
    description_km: props.item?.description_km,
    description_en: props.item?.description_en,
    company_id: props.item?.company_id || null,
    modifications: props.item?.modifications || [],
    images: props.item?.images
        ? props.item.images.map((img) => ({
              image: img.image || '',
              is_main: img.is_main || 0,
          }))
        : [],
});

const addModification = () => {
    form.modifications.push({
        id: null,
        modification_name: '',
        unit: '',
        modification: 0,
    });
};
const addImage = () => {
    form.images.push('');
};

watch(
    () => form.images,
    (newImages) => {
        const mainIndex = newImages.findIndex((img: { is_main: number }) => img.is_main === 1);
        mainImageIndex.value = mainIndex >= 0 ? mainIndex : null;
    },
    { immediate: true, deep: true }, // deep is important here
);

function setMainImage(index: number) {
    form.images = form.images.map((img: { image: string; is_main: number }, i: number) => ({
        ...img,
        is_main: i === index ? 1 : 0,
    }));
    mainImageIndex.value = index;
}

const removeImage = (index: number) => {
    form.images.splice(index, 1);

    if (mainImageIndex.value === index) {
        mainImageIndex.value = null;
    } else if (mainImageIndex.value && mainImageIndex.value > index) {
        // Shift mainImageIndex if needed
        mainImageIndex.value -= 1;
    }
};

const removeModification = (index: number) => {
    form.modifications.splice(index, 1);
};

const submit = () => {
    form.images = form.images.map((imgObj: { image: any; is_main: any }) => {
        if (typeof imgObj === 'string') {
            // If it's string only, wrap into object with default is_main 0
            return { image: imgObj, is_main: 0 };
        }
        if (imgObj && typeof imgObj === 'object' && 'image' in imgObj) {
            return {
                image: imgObj.image || '',
                is_main: imgObj.is_main || 0,
            };
        }
        return { image: '', is_main: 0 };
    });

    const url = isEditMode.value ? route('items.update', props.item!.id) : route('items.store');
    const method = isEditMode.value ? 'put' : 'post';

    form[method](url, {
        onSuccess: () => {
            toast({
                variant: 'default',
                duration: 3000,
                description: isEditMode.value ? trans('item.messages.item_updated_successfully') : trans('item.messages.item_created_successfully'),
            });
        },
        onError: () => {
            toast({
                variant: 'destructive',
                duration: 3000,
                description: isEditMode.value ? trans('item.messages.item_update_failed') : trans('item.messages.item_create_failed'),
            });
        },
    });
};

provide('form', form);
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <Card class="h-full md:col-span-1">
                <CardHeader>
                    <CardTitle>{{ trans('item.form.item_info') }}</CardTitle>
                    <CardDescription>{{ trans('item.form.basic_info') }}</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="space-y-2">
                        <Label for="company">{{ trans('item.form.company') }}</Label>
                        <Combobox
                            v-model="form.company_id"
                            :items="companies"
                            :empty-text="trans('item.form.no_company_found')"
                            :placeholder="trans('item.form.select_company')"
                            :default-label="trans('item.form.select')"
                            :search="trans('item.form.search')"
                        />
                        <p v-if="form.errors.company_id" class="text-xs text-destructive">{{ form.errors.company_id }}</p>
                    </div>
                    <MultiLangInput en="name_en" km="name_km" :title="trans('company.form.fields.name')" />

                    <MultiLangTextArea en="description_en" km="description_km" :title="trans('company.form.fields.description')" />

                    <div class="space-y-2">
                        <Label class="text-base font-medium">{{ trans('item.form.images') }}</Label>

                        <!-- No image message -->
                        <div v-if="form.images.length === 0" class="text-sm">
                            {{ trans('item.form.no_images_added') }}
                        </div>

                        <div v-if="form.images.length > 0" class="grid grid-cols-3 gap-4">
                            <div v-for="(img, index) in form.images" :key="index" class="relative rounded-lg pt-5 transition">
                                <div
                                    class="relative flex w-full cursor-pointer items-center justify-center rounded-md border px-2 py-10 transition-colors"
                                    :class="{
                                        'bg-muted': mainImageIndex !== index,
                                        'border-primary bg-primary/10': mainImageIndex === index,
                                    }"
                                    @click="setMainImage(index)"
                                >
                                    <UploadImage v-model="form.images[index].image" class="max-h-full max-w-full object-contain" />

                                    <!-- Main image badge -->
                                    <div
                                        v-if="mainImageIndex === index"
                                        class="absolute top-2 right-2 rounded bg-primary px-2 py-1 text-xs font-medium text-primary-foreground"
                                    >
                                        Main
                                    </div>

                                    <!-- Click to set main text -->
                                    <div v-else class="absolute right-2 bottom-2 left-2 text-center text-xs text-muted-foreground">
                                        Click to set as main
                                    </div>
                                </div>

                                <Button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    class="absolute top-2 -left-3 rounded-full border bg-white/90 shadow hover:bg-white"
                                    @click.stop="removeImage(index)"
                                >
                                    <Trash2 class="h-4 w-4 text-destructive" />
                                </Button>
                            </div>
                        </div>

                        <!-- Add Image Button -->
                        <div>
                            <Button type="button" variant="outline" size="sm" class="mt-2 inline-flex items-center gap-2" @click="addImage">
                                <Plus class="h-4 w-4" />
                                {{ trans('item.form.add_image') }}
                            </Button>
                        </div>

                        <!-- Error -->
                        <p v-if="form.errors.images" class="text-sm text-destructive">
                            {{ form.errors.images }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Modifications (2/3) -->
            <Card class="h-full md:col-span-1">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>{{ trans('item.form.modifications') }}</CardTitle>
                            <CardDescription>{{ trans('item.form.modifications_description') }}</CardDescription>
                        </div>
                        <Button type="button" variant="outline" size="sm" @click="addModification" class="flex items-center gap-2">
                            <Plus class="h-4 w-4" />
                            {{ trans('item.form.add_modification') }}
                        </Button>
                    </div>
                </CardHeader>

                <CardContent class="space-y-6">
                    <div v-if="form.modifications.length === 0" class="py-8 text-center text-muted-foreground">
                        <p>{{ trans('item.form.no_modifications') }}</p>
                        <Button type="button" variant="outline" size="sm" @click="addModification" class="mt-4">
                            <Plus class="mr-2 h-4 w-4" />
                            {{ trans('item.form.add_first_modification') }}
                        </Button>
                    </div>

                    <div v-for="(modification, index) in form.modifications" :key="index" class="relative space-y-4 rounded-lg border bg-muted p-6">
                        <Button
                            type="button"
                            variant="destructive"
                            size="sm"
                            class="absolute top-3 right-3 h-8 w-8 p-0"
                            @click="removeModification(index)"
                        >
                            <Trash2 class="h-4 w-4" />
                        </Button>

                        <div class="grid grid-cols-1 gap-4 pr-12 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label :for="`modification_name_${index}`"> {{ trans('item.form.modification_name') }} <RequiredMark /> </Label>
                                <Input
                                    :id="`modification_name_${index}`"
                                    v-model="modification.modification_name"
                                    :placeholder="trans('item.form.modification_name_placeholder')"
                                    :class="{ 'border-red-500': form.errors[`modifications.${index}.modification_name`] }"
                                />
                                <p v-if="form.errors[`modifications.${index}.modification_name`]" class="text-sm text-destructive">
                                    {{ form.errors[`modifications.${index}.modification_name`] }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label :for="`unit_${index}`">{{ trans('item.form.unit') }} <RequiredMark /></Label>

                                <Combobox
                                    v-model="modification.unit"
                                    :items="unitOptions"
                                    :empty-text="trans('item.form.no_company_found')"
                                    :placeholder="trans('item.form.select_company')"
                                    :default-label="trans('item.form.select')"
                                    :search="trans('item.form.search')"
                                />
                                <p v-if="form.errors[`modifications.${index}.unit`]" class="text-sm text-destructive">
                                    {{ form.errors[`modifications.${index}.unit`] }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label :for="`modification_price_${index}`"> {{ trans('item.form.unit_price') }} <RequiredMark /> </Label>
                                <Input
                                    :id="`modification_price_${index}`"
                                    v-model="modification.unit_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :placeholder="trans('item.form.unit_price')"
                                    :class="{ 'border-red-500': form.errors[`modifications.${index}.unit_price`] }"
                                />
                                <p v-if="form.errors[`modifications.${index}.unit_price`]" class="text-sm text-destructive">
                                    {{ form.errors[`modifications.${index}.unit_price`] }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <p v-if="form.errors[`modifications`]" class="text-sm text-destructive">
                        {{ form.errors[`modifications`] }}
                    </p>
                </CardContent>

                <!-- Submit Actions moved to CardFooter -->
                <CardFooter class="flex items-end justify-end gap-4">
                    <CancelButton :fallback-route="route('items.index')" />
                    <Button type="submit" :disabled="form.processing" class="min-w-[120px]">
                        <span v-if="form.processing">
                            {{ isEditMode ? trans('item.button.updating') : trans('item.button.creating') }}
                        </span>
                        <span v-else>
                            {{ isEditMode ? trans('item.button.update') : trans('item.button.create') }}
                        </span>
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </form>
</template>
