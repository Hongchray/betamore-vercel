import { useToast } from '@/components/ui/toast/use-toast';
import { useConfirmDialog } from '@/composables/ui/delete-dialog/useDeleteDialog';
import { useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { ref } from 'vue';
import { route } from 'ziggy-js';

const { toast } = useToast();
const errors = ref();

export const onDelete = (id: number | string) => {
    const { confirmAction } = useConfirmDialog();
    const form = useForm({});

    let isDeleting = false;

    confirmAction({
        title: trans('delivery.actions.delete_confirm_title'),
        description: trans('delivery.actions.delete_confirm_description'),
        onConfirm: () => {
            if (isDeleting) return;
            isDeleting = true;

            form.delete(route('deliveries.destroy', id), {
                onError: (error) => {
                    errors.value = error;
                    toast({
                        variant: 'destructive',
                        duration: 3000,
                        description: error.message || trans('delivery.actions.delete_failed'),
                    });
                },
                onSuccess: () => {
                    toast({
                        variant: 'default',
                        duration: 3000,
                        description: trans('delivery.actions.delete_success'),
                    });
                },
            });
        },
    });
};
