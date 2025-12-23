<template>
    <Dialog :open="visible" @update:open="$emit('update:visible', $event)">
        <DialogContent class="overflow-hidden p-0 sm:max-w-xl md:max-w-3xl lg:max-w-4xl">
            <DialogTitle class="flex items-center justify-between border-b p-4">
                <span>{{ trans('composable.cropper.title') }}</span>
                <div class="flex items-center gap-2 px-8">
                    <div>{{ trans('composable.cropper.option') }}:</div>
                    <Button variant="outline" size="sm" :class="{ 'bg-muted text-primary': cropMode === 'free' }" @click="setCropMode('free')">
                        <span class="text-xs">{{ trans('composable.cropper.free_crop') }}</span>
                    </Button>
                    <Button variant="outline" size="sm" :class="{ 'bg-muted text-primary': cropMode === 'square' }" @click="setCropMode('square')">
                        <span class="text-xs">{{ trans('composable.cropper.square') }}</span>
                    </Button>
                    <Button variant="outline" size="sm" :class="{ 'bg-muted text-primary': cropMode === 'banner' }" @click="setCropMode('banner')">
                        <span class="text-xs">{{ trans('composable.cropper.banner') }} (330×200)</span>
                    </Button>
                </div>

                <div></div>
            </DialogTitle>

            <div class="relative bg-muted/10">
                <div class="cropper-container w-full" :style="{ height: cropperHeight }">
                    <img ref="imageRef" :src="image" class="max-w-full" @load="initializeCropper" />
                </div>

                <!-- Loading indicator -->
                <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center bg-background/70">
                    <div class="h-8 w-8 animate-spin rounded-full border-4 border-primary border-t-transparent"></div>
                </div>
            </div>

            <div class="flex justify-between gap-2 border-t p-4">
                <Button variant="outline" @click="$emit('cancel')">{{ trans('composable.cropper.cancel') }}</Button>
                <div class="flex gap-2">
                    <Button variant="secondary" @click="uploadWithoutCrop">
                        {{ trans('composable.cropper.upload_original') }}
                    </Button>
                    <Button @click="confirmCrop">
                        {{ trans('composable.cropper.crop_and_upload') }}
                    </Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogTitle } from '@/components/ui/dialog';
import Cropper from 'cropperjs';
import { trans } from 'laravel-vue-i18n';
import { computed, nextTick, onUnmounted, ref, watch } from 'vue';

const cropMode = ref<'free' | 'square' | 'banner'>('square');

const props = defineProps<{
    image: string;
    visible: boolean;
    fileName?: string; // Optional filename prop
}>();

const emit = defineEmits(['update:visible', 'confirm', 'cancel']);

const imageRef = ref<HTMLImageElement | null>(null);
const isLoading = ref(true);
const useFixedRatio = ref(true);
let cropper: Cropper | null = null;

// Compute an appropriate height for the cropper container
const cropperHeight = computed(() => {
    return 'calc(min(70vh, 600px))';
});

// Get file name without extension
const getFileNameWithoutExtension = () => {
    if (props.fileName) {
        // Remove extension from filename
        return props.fileName.replace(/\.[^/.]+$/, '');
    }
    // Default base name if no filename provided
    return 'image';
};

// Get file extension from original image URL or source
const getFileExtension = () => {
    // Try to get extension from provided fileName prop
    if (props.fileName) {
        const match = props.fileName.match(/\.([^.]+)$/);
        if (match) return match[1].toLowerCase();
    }

    // Try to extract from image URL
    const urlMatch = props.image.match(/\.([^.?#]+)(?:\?|#|$)/);
    if (urlMatch) return urlMatch[1].toLowerCase();

    // Default to jpg if no extension found
    return 'jpg';
};

// Generate a new filename with proper extension
const generateFileName = (prefix = '') => {
    const baseName = prefix || getFileNameWithoutExtension();
    const extension = getFileExtension();

    return `${baseName}.${extension}`;
};

watch(
    () => props.visible,
    async (val) => {
        if (val) {
            isLoading.value = true;
            await nextTick();
            if (imageRef.value) {
                initializeCropper();
            }
        } else {
            if (cropper) cropper.destroy();
        }
    },
);

onUnmounted(() => {
    if (cropper) cropper.destroy();
});

const confirmCrop = () => {
    if (cropper) {
        let options: Cropper.GetCroppedCanvasOptions = {};

        if (cropMode.value === 'square') {
            options = { width: 512, height: 512 };
        } else if (cropMode.value === 'banner') {
            options = { width: 330, height: 200 };
        }

        const canvas = cropper.getCroppedCanvas(options);
        canvas.toBlob((blob) => {
            if (blob) {
                const file = new File([blob], generateFileName(), {
                    type: 'image/jpeg',
                });
                emit('confirm', file);
            }
        }, 'image/jpeg');
    }
};

const uploadWithoutCrop = async () => {
    try {
        // Convert the original image to blob without cropping
        const response = await fetch(props.image);
        const blob = await response.blob();

        // Create a File object with proper name and extension
        const file = new File([blob], generateFileName(), {
            type: blob.type,
        });

        emit('confirm', file);
    } catch (error) {
        console.error('Error uploading original image:', error);
    }
};

const initializeCropper = () => {
    if (cropper) cropper.destroy();
    if (imageRef.value) {
        cropper = new Cropper(imageRef.value, {
            aspectRatio: useFixedRatio.value ? 1 : NaN, // NaN for free aspect ratio
            viewMode: 1,
            autoCropArea: 1,
            zoomable: true,
            responsive: true,
            restore: false,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
            ready() {
                isLoading.value = false;
            },
        });
    }
};

const setCropMode = (mode: 'free' | 'square' | 'banner') => {
    cropMode.value = mode;
    updateCropperAspectRatio();
};

const updateCropperAspectRatio = () => {
    if (cropper) {
        let aspect: number | null = null;
        if (cropMode.value === 'square') aspect = 1;
        else if (cropMode.value === 'banner') aspect = 330 / 200;
        else aspect = NaN;

        cropper.setAspectRatio(aspect);
    }
};
</script>

<style>
/* Import Cropper.js styles */
@import 'cropperjs/dist/cropper.css';

/* Additional styles to improve cropper appearance */
.cropper-container {
    background-color: transparent;
}

.cropper-view-box {
    outline: 1px solid #fff;
    outline-color: rgba(255, 255, 255, 0.75);
}

.cropper-line,
.cropper-point {
    background-color: #fff;
}
</style>
