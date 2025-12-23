import { toast } from '@/components/ui/toast';
import { getCsrfToken } from '@/utils/sharedData';
import { ref } from 'vue';

export function useUploadImage(initialUrl: string = '', folder: string = '') {
    const url = ref(initialUrl);
    const fileInput = ref<HTMLInputElement | null>(null);
    const uploadImageLoading = ref(false);

    const openFileUpload = () => {
        fileInput.value?.click();
    };

    const handleFileUpload = (event: Event) => {
        const target = event.target as HTMLInputElement;
        if (target.files && target.files.length > 0) {
            const file = target.files[0];
            uploadImage(file);
        }
    };

    const uploadImage = async (file: File | Blob) => {
        uploadImageLoading.value = true;
        try {
            const csrfToken = await getCsrfToken();
            const formData = new FormData();

            if (url.value) {
                const oldPath = url.value.replace(import.meta.env.VITE_DO_URL + '/', '');
                formData.append('old_image', oldPath);
                console.log('old_path', oldPath);
            }
            formData.append('image', file);
            formData.append('folder', folder);

            const response = await fetch(`/admin/image`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
            });
            if (!response.ok) {
                toast({
                    variant: 'destructive',
                    duration: 3000,
                    description: 'Failed to upload image.',
                });
                return;
            }
            const data = await response.json();
            url.value = data.url; // Assuming the server returns the uploaded image URL
        } catch (error) {
            console.error('Error upload image:', error);
            toast({
                variant: 'destructive',
                duration: 3000,
                description: 'Error uploading image: File size exceeds 2MB.',
            });
        } finally {
            uploadImageLoading.value = false;
        }
    };

    return {
        url,
        fileInput,
        uploadImageLoading,
        openFileUpload,
        handleFileUpload,
        uploadImage,
    };
}
