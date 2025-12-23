import { Textarea } from '@/components/ui/textarea';
import AlertDialog from '@/composables/ui/delete-dialog/delete-dialog.vue';
import { h, ref, render } from 'vue';

export function useConfirmDialog() {
    const isOpen = ref(false);
    const note = ref('');
    const container = document.createElement('div');
    let confirmCallback: ((note?: string) => void) | null = null;

    function confirmAction({
        title = '',
        description = '',
        sumitButtonTitle = '',
        onConfirm = () => {},
        withNote = false,
    }: {
        title?: string;
        description?: string;
        sumitButtonTitle?: string;
        onConfirm?: (note?: string) => void;
        withNote?: boolean;
    }) {
        confirmCallback = onConfirm;
        isOpen.value = true;

        render(
            h(
                AlertDialog,
                {
                    open: isOpen.value,
                    onConfirm: () => {
                        if (confirmCallback) {
                            confirmCallback(withNote ? note.value : undefined);
                            cleanup();
                        }
                    },
                    onCancel: cleanup,
                    title,
                    description,
                    sumitButtonTitle,
                    withNote,
                    note,
                },
                {
                    default: () => [
                        h('div', { class: 'dialog-header space-y-2' }, [
                            withNote
                                ? h(Textarea, {
                                      class: 'mt-4 w-full p-2 border rounded',
                                      rows: 4,
                                      required: true,
                                      placeholder: 'Add a note (optional)',
                                      modelValue: note.value,
                                      'onUpdate:modelValue': (value: string | number) => (note.value = value.toString()),
                                  })
                                : null,
                        ]),
                    ],
                },
            ),
            container,
        );

        document.body.appendChild(container);
    }

    function cleanup() {
        render(null, container);
        if (document.body.contains(container)) {
            document.body.removeChild(container);
        }
        note.value = '';
    }

    return {
        confirmAction,
    };
}
