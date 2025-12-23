<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useToast } from '@/components/ui/toast/use-toast';
import CancelButton from '@/composables/ui/cancel-button/CancelButton.vue';
import Combobox from '@/composables/ui/combobox/Combobox.vue';
import DatePicker from '@/composables/ui/date-picker/DatePicker.vue';
import ImagePreview from '@/composables/ui/image-preview/ImagePreview.vue';
import MultiLangInput from '@/composables/ui/multilang/MultiLangInput.vue';
import MultiLangTextArea from '@/composables/ui/multilang/MultiLangTextArea.vue';
import UploadImage from '@/composables/ui/upload-image/UploadImage.vue';
import { useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { Loader2 } from 'lucide-vue-next';
import { computed, provide, ref } from 'vue';
import { route } from 'ziggy-js';
import { Promotion } from '../data/schema';
interface ComboboxItem {
    value: string;
    label: string;
}

const typeValues = ['percent', 'flat'];

const typeOption = computed(() =>
    typeValues.map((unit) => ({
        label: trans(`promotion.form.type.${unit}`),
        value: unit,
    })),
);

type Item = {
    id: string;
    name_en: string;
    item_id: string;
    first_image?: {
        image: string;
    };
};

const props = defineProps<{
    promotion?: Promotion | null;
    items: Item[];
}>();

const { toast } = useToast();
const isEditMode = computed(() => !!props.promotion);
const submitButtonText = computed(() => (isEditMode.value ? trans('promotion.button.update') : trans('promotion.button.create')));

const form = useForm<Promotion>({
    id: props.promotion?.id ?? '',
    banner: props.promotion?.banner ?? '',
    name_en: props.promotion?.name_en ?? '',
    name_km: props.promotion?.name_km ?? '',
    description_en: props.promotion?.description_en ?? '',
    description_km: props.promotion?.description_km ?? '',
    type: props.promotion?.type ?? 'percent',
    discount_value: props.promotion?.discount_value ?? 0,
    start_date: props.promotion?.start_date ?? '',
    end_date: props.promotion?.end_date ?? '',
    items: props.promotion?.items ?? [],
});

const dialogOpen = ref(false);
const selectedIds = ref<string[]>([]);
const typePlaceholder = computed(() => trans('promotion.form.fields.type'));

const openDialog = () => {
    dialogOpen.value = true;
    selectedIds.value = [...form.items];
};
const promotionItemsCount = computed(() => {
    return form.items?.length || 0;
});

const areAllSelected = computed(() => {
    return props.items.length > 0 && selectedIds.value.length === props.items.length;
});

function toggleSelectAll() {
    if (areAllSelected.value) {
        selectedIds.value = [];
    } else {
        selectedIds.value = props.items.map((item) => item.id);
    }
}

function toggleSelection(id: string) {
    const index = selectedIds.value.indexOf(id);
    if (index > -1) {
        selectedIds.value.splice(index, 1);
    } else {
        selectedIds.value.push(id);
    }
}

const selectedItemsDetails = computed(() => {
    if (!form.items || form.items.length === 0) return [];

    return form.items
        .map((itemId) => {
            const item = props.items.find((i) => i.id === itemId);
            return item
                ? {
                      id: itemId,
                      name: item.name_en,
                      item_id: item.item_id,
                  }
                : null;
        })
        .filter(Boolean);
});
const confirmSelection = () => {
    form.items = [...selectedIds.value];
    dialogOpen.value = false;
};

const removeItem = (itemId: string) => {
    form.items = form.items.filter((id) => id !== itemId);
};

const handleSubmit = () => {
    const url = isEditMode.value ? route('promotions.update', props.promotion!.id) : route('promotions.store');
    const method = isEditMode.value ? 'put' : 'post';

    const submitData = {
        name_en: form.name_en,
        name_km: form.name_km,
        banner: form.banner,
        description_en: form.description_en,
        description_km: form.description_km,
        type: form.type,
        discount_value: form.discount_value,
        start_date: form.start_date,
        end_date: form.end_date,
        start_time: form.start_time,
        end_time: form.end_time,
        items: form.items,
    };

    console.log('Final submit data:', submitData);

    form.transform(() => submitData)[method](url, {
        onSuccess: () => {
            toast({
                variant: 'default',
                description: trans('promotion.messages.saved_successfully'),
                duration: 3000,
            });
        },
        onError: (error) => {
            console.error('Form submission error:', error);
            console.error('Form errors:', form.errors);
            toast({
                variant: 'destructive',
                description: error?.message || trans('promotion.messages.save_failed'),
                duration: 3000,
            });
        },
    });
};
provide('form', form);
</script>

<template>
    <Card class="w-full rounded-2xl border border-border bg-background p-8 shadow-md dark:bg-muted/10">
        <CardHeader class="mb-6 space-y-1">
            <CardTitle class="text-2xl font-bold text-foreground dark:text-white">{{ submitButtonText }}</CardTitle>
            <CardDescription class="text-sm text-muted-foreground dark:text-gray-400">
                {{ trans('promotion.form.description') }}
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form @submit.prevent="handleSubmit" class="space-y-8">
                <!-- Banner Upload -->
                <div class="space-y-2">
                    <Label for="banner" class="text-sm font-medium text-foreground dark:text-white">
                        {{ trans('promotion.form.fields.banner') }}
                    </Label>
                    <UploadImage
                        v-model="form.banner"
                        type="banner"
                        :class="{ 'border-red-500': form.errors.banner }"
                        @update:modelValue="
                            (value) => {
                                form.banner = value;
                            }
                        "
                    />
                    <p v-if="form.errors.banner" class="text-xs text-red-500">{{ form.errors.banner }}</p>
                </div>

                <!-- MultiLang Inputs -->
                <MultiLangInput en="name_en" km="name_km" :title="trans('promotion.form.fields.name')" />
                <MultiLangTextArea en="description_en" km="description_km" :title="trans('promotion.form.fields.description')" />

                <!-- Type & Discount -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="type" class="text-foreground dark:text-white">
                            {{ trans('promotion.form.fields.type') }}
                        </Label>

                        <Combobox
                            v-model="form.type"
                            :items="typeOption"
                            :empty-text="trans('item.form.no_company_found')"
                            :placeholder="trans('item.form.select_company')"
                            :default-label="trans('item.form.select')"
                            :search="trans('item.form.search')"
                        />

                        <p v-if="form.errors.type" class="text-sm text-destructive">
                            {{ form.errors.type }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="amount" class="text-foreground dark:text-white">{{ trans('promotion.form.fields.amount') }}</Label>
                        <Input
                            id="amount"
                            type="number"
                            min="0"
                            step="any"
                            v-model.number="form.discount_value"
                            placeholder="0"
                            class="w-full bg-background text-foreground dark:bg-muted/10 dark:text-white"
                        />
                        <p v-if="form.errors.discount_value" class="text-sm text-destructive">{{ form.errors.discount_value }}</p>
                    </div>
                </div>

                <!-- Date & Time -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="space-y-2">
                        <Label for="start_date" class="text-foreground dark:text-white">
                            {{ trans('promotion.form.fields.start_date') }} <span class="text-red-500">*</span>
                        </Label>
                        <DatePicker v-model="form.start_date" :placeholder="trans('promotion.form.fields.start_date')" />
                        <p v-if="form.errors.start_date" class="text-sm text-destructive">{{ form.errors.start_date }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="end_date" class="text-foreground dark:text-white">
                            {{ trans('promotion.form.fields.end_date') }} <span class="text-red-500">*</span>
                        </Label>
                        <DatePicker v-model="form.end_date" :placeholder="trans('promotion.form.fields.end_date')" />
                        <p v-if="form.errors.end_date" class="text-sm text-destructive">{{ form.errors.end_date }}</p>
                    </div>
                </div>

                <!-- Items -->
                <div class="space-y-2">
                    <Label class="font-medium text-foreground dark:text-white">{{ trans('promotion.form.fields.items') }}</Label>

                    <div class="flex flex-wrap items-center gap-3">
                        <Button type="button" @click="openDialog" class="flex items-center gap-2">
                            {{ trans('promotion.form.buttons.select_items') }}
                            <span v-if="promotionItemsCount > 0" class="ml-1 rounded-full bg-primary px-2 py-1 text-xs text-primary-foreground">
                                {{ promotionItemsCount }}
                            </span>
                        </Button>

                        <div v-if="promotionItemsCount > 0" class="flex items-center gap-2">
                            <div class="rounded-lg bg-muted px-3 py-2 text-sm dark:bg-muted/20">
                                <span class="font-medium">{{ promotionItemsCount }}</span>
                                <span class="ml-1 text-muted-foreground">
                                    {{ promotionItemsCount === 1 ? trans('promotion.form.item') : trans('promotion.form.items') }}
                                    {{ trans('promotion.form.selected') }}
                                </span>
                            </div>

                            <div class="max-w-md truncate text-xs text-muted-foreground dark:text-gray-400">
                                <template v-if="selectedItemsDetails.length > 0">
                                    {{
                                        selectedItemsDetails
                                            .slice(0, 2)
                                            .map((item) => (item ? item.name : ''))
                                            .join(', ')
                                    }}
                                    <span v-if="selectedItemsDetails.length > 2">
                                        {{ trans('promotion.form.and') }} {{ selectedItemsDetails.length - 2 }} {{ trans('promotion.form.more') }}
                                    </span>
                                </template>
                            </div>
                        </div>

                        <div v-else class="text-sm text-muted-foreground dark:text-gray-400">
                            {{ trans('promotion.form.no_selecte_item') }}
                        </div>
                    </div>

                    <details v-if="promotionItemsCount > 0" class="mt-3">
                        <summary class="cursor-pointer text-sm text-muted-foreground hover:text-foreground dark:hover:text-white">
                            {{ trans('promotion.form.view_select_item') }} ({{ promotionItemsCount }})
                        </summary>
                        <div class="mt-2 max-h-32 overflow-y-auto rounded-md border bg-muted/30 p-2 dark:bg-muted/20">
                            <div class="flex flex-wrap gap-1">
                                <div
                                    v-for="item in selectedItemsDetails"
                                    :key="item.id"
                                    class="group inline-flex max-w-xs items-center truncate rounded-full bg-background px-2 py-1 text-xs shadow-sm transition hover:bg-accent dark:bg-muted/10"
                                >
                                    <span class="truncate text-foreground dark:text-white">{{ item.name }}</span>
                                    <button
                                        type="button"
                                        @click="removeItem(item.id)"
                                        class="ml-1 text-xs text-muted-foreground transition hover:text-destructive dark:hover:text-red-500"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                    </details>

                    <p v-if="form.errors.items" class="text-sm text-destructive">{{ form.errors.items }}</p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4 pt-6">
                    <CancelButton :fallback-route="route('promotions.index')" />
                    <Button type="submit" :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        {{ submitButtonText }}
                    </Button>
                </div>
            </form>

            <!-- Dialog -->
            <Dialog v-model:open="dialogOpen">
                <DialogContent class="rounded-xl bg-background p-6 dark:bg-muted">
                    <DialogHeader>
                        <DialogTitle class="text-xl font-semibold tracking-tight text-foreground dark:text-white">
                            {{ trans('promotion.form.dialog.title') }}
                        </DialogTitle>
                    </DialogHeader>

                    <div class="mt-4 max-h-[800px] overflow-auto rounded-md border border-border shadow-sm dark:border-zinc-700">
                        <table class="w-full table-auto border border-border text-sm dark:border-zinc-700">
                            <thead class="sticky top-0 z-10 bg-gray-50 text-left shadow-sm dark:border-b dark:border-zinc-700 dark:bg-muted/20">
                                <tr class="border-b border-border text-sm font-medium text-muted-foreground dark:border-zinc-700 dark:text-gray-400">
                                    <th class="w-12 border-border px-4 py-3 text-center dark:border-zinc-700">
                                        <input
                                            type="checkbox"
                                            :checked="areAllSelected"
                                            @change="toggleSelectAll"
                                            class="form-checkbox h-4 w-4 rounded text-primary"
                                        />
                                    </th>
                                    <th class="border-border px-4 py-3 dark:border-zinc-700">
                                        {{ trans('promotion.form.dialog.table.headers.id') }}
                                    </th>
                                    <th class="border-border px-4 py-3 dark:border-zinc-700">
                                        {{ trans('promotion.form.dialog.table.headers.name') }}
                                    </th>
                                    <th class="px-4 py-3">
                                        {{ trans('promotion.form.dialog.table.headers.logo') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in props.items"
                                    :key="item.id"
                                    @click="toggleSelection(item.id)"
                                    class="cursor-pointer border-t border-border transition-colors hover:bg-accent/40 dark:border-zinc-700"
                                    :class="{ 'bg-muted/60 dark:bg-muted/30': selectedIds.includes(item.id) }"
                                >
                                    <td class="border-border px-4 py-2 text-center dark:border-zinc-700">
                                        <input
                                            type="checkbox"
                                            :value="item.id"
                                            v-model="selectedIds"
                                            @click.stop
                                            class="form-checkbox h-4 w-4 rounded text-primary"
                                        />
                                    </td>
                                    <td class="border-border px-4 py-2 dark:border-zinc-700">
                                        <span class="font-medium text-foreground dark:text-white">{{ item.item_id }}</span>
                                    </td>
                                    <td class="border-border px-4 py-2 dark:border-zinc-700">
                                        <span class="font-medium text-foreground dark:text-white">{{ item.name_en }}</span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <ImagePreview
                                            v-if="item.first_image"
                                            :image="item.first_image.image"
                                            class="h-10 w-10 rounded object-cover"
                                        />
                                        <span v-else class="text-xs text-muted-foreground italic dark:text-gray-400">No image</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <DialogFooter class="mt-6 flex justify-end gap-3">
                        <Button variant="outline" @click="dialogOpen = false">
                            {{ trans('promotion.form.buttons.cancel') }}
                        </Button>

                        <Button @click="confirmSelection">
                            {{ trans('promotion.form.buttons.confirm') }}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </CardContent>
    </Card>
</template>
