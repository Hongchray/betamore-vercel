import { ref } from 'vue';

export interface ToastItem {
    id: string;
    type: 'success' | 'error' | 'warning' | 'info';
    title: string;
    description?: string;
    duration?: number;
}

const toasts = ref<ToastItem[]>([]);

export const useToast = () => {
    const addToast = (toast: Omit<ToastItem, 'id'>) => {
        const id = Date.now().toString();
        const newToast = { ...toast, id };
        toasts.value.push(newToast);

        // Auto remove after duration
        setTimeout(() => {
            removeToast(id);
        }, toast.duration || 4000);

        return id;
    };

    const removeToast = (id: string) => {
        const index = toasts.value.findIndex((t) => t.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
    };

    const toast = {
        success: (title: string, description?: string) => addToast({ type: 'success', title, description }),
        error: (title: string, description?: string) => addToast({ type: 'error', title, description }),
        warning: (title: string, description?: string) => addToast({ type: 'warning', title, description }),
        info: (title: string, description?: string) => addToast({ type: 'info', title, description }),
    };

    return {
        toasts,
        toast,
        addToast,
        removeToast,
    };
};
