//Users/apple/Documents/Focuz Solution/betamore-admin/resources/js/pages/Customer/data/events.ts
import { useToast } from '@/components/ui/toast/use-toast';
import { useConfirmDialog } from '@/composables/ui/delete-dialog/useDeleteDialog';
import { useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { ref } from 'vue';
import { route } from 'ziggy-js';

const { toast } = useToast();
const errors = ref();

export const onDelete = (data: any) => {
    const { confirmAction } = useConfirmDialog();
    const form = useForm({});

    let isDeleting = false;

    confirmAction({
        title: trans('customer.actions.delete_confirm_title'),
        description: trans('customer.actions.delete_confirm_description'),
        onConfirm: () => {
            if (isDeleting) return;
            isDeleting = true;

            const id = data?.id;

            form.delete(route('customers.destroy', id), {
                onError: (error) => {
                    toast({
                        variant: 'destructive',
                        duration: 3000,
                        description: error.message || trans('customer.actions.delete_failed'),
                    });
                },
                onSuccess: () => {
                    toast({
                        variant: 'default',
                        duration: 3000,
                        description: data.deleted_at ? trans('customer.actions.force_delete_success') : trans('customer.actions.delete_success'),
                    });
                },
            });
        },
    });
};
