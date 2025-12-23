<template>
    <div class="flex flex-col items-start justify-start gap-4" :class="type == 'banner' ? 'flex-col items-start' : 'items-center'">
        <ImagePreview v-if="displayUrl || url" :image="displayUrl || url" :className="type === 'banner' ? 'w-[330px] h-[200px]' : 'w-[150px]'" />
        <Avatar class="h-[150px] rounded-md border" v-else :class="type === 'banner' ? 'h-[200px] w-[330px]' : 'w-[150px]'">
            <AvatarImage :src="displayUrl || url" class="!object-contain" />
            <AvatarFallback>
                <div class="flex flex-col items-center justify-center gap-4">
                    <Image class="h-12 w-12 text-gray-500" />
                    <h1 class="text-gray-500">
                        {{ size }}
                    </h1>
                </div>
            </AvatarFallback>
        </Avatar>

        <!-- Rest of your template remains the same -->
        <Button
            :class="type === 'admin' ? 'w-full' : type === 'adminDetail' ? 'hidden' : 'w-[150px]'"
            @click="openFileUpload"
            variant="outline"
            type="button"
            :disabled="uploadImageLoading"
        >
            <div v-if="uploadImageLoading" class="flex gap-2">
                <Loader2 class="mr-2 h-4 w-4 animate-spin" />
                {{ trans('composable.upload_image.wait') }}
            </div>
            <div v-else>
                {{ trans('composable.upload_image.change_photo') }}
            </div>
        </Button>
        <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="handleFileUploadWithCrop" />
        <CropperModal
            v-if="rawImage"
            :image="rawImage"
            :visible="showCropper"
            :fileName="originalFileName"
            @update:visible="showCropper = $event"
            @cancel="showCropper = false"
            @confirm="handleCropConfirm"
        />
    </div>
</template>

<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import ImagePreview from '@/composables/ui/image-preview/ImagePreview.vue';
import { trans } from 'laravel-vue-i18n';
import { Image, Loader2 } from 'lucide-vue-next';
import { computed, onUnmounted, ref, watch } from 'vue';
import CropperModal from './CropperModal.vue';
import { useUploadImage } from './useUploadImage';
const props = defineProps<{
    modelValue?: string;
    type?: string;
    size?: string;
    folder?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const { url, fileInput, uploadImageLoading, openFileUpload, handleFileUpload, uploadImage } = useUploadImage(props.modelValue, props.folder);

watch(url, (newValue) => {
    emit('update:modelValue', newValue);
});
// Create a computed property to handle image URL formatting
const displayUrl = computed(() => {
    if (!props.modelValue) return '';
    if (props.modelValue.startsWith('http')) return props.modelValue;
    if (props.modelValue.startsWith('/')) return props.modelValue;
    return `/${props.modelValue}`; // Add leading slash for relative paths
});
//Crop
const showCropper = ref(false);
const rawImage = ref<string | null>(null);
const originalFileName = ref<string>('');

function handleFileUploadWithCrop(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file || !file.type.startsWith('image/')) return;

    // Store the original file name
    originalFileName.value = file.name;

    // Create object URL for the cropper
    rawImage.value = URL.createObjectURL(file);
    showCropper.value = true;
}

async function handleCropConfirm(croppedFile: File) {
    showCropper.value = false;

    // Clean up object URL to prevent memory leaks
    if (rawImage.value) {
        URL.revokeObjectURL(rawImage.value);
        rawImage.value = null;
    }

    // Use croppedFile which now should be a File object with proper name and extension
    await uploadImage(croppedFile);
}

// Clean up object URL when component is unmounted
onUnmounted(() => {
    if (rawImage.value) {
        URL.revokeObjectURL(rawImage.value);
    }
});
</script>
