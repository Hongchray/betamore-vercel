import { ActionType } from '@/enums/action_menu';
import { ToolbarAction } from '@/interfaces/ITable';
import { trans } from 'laravel-vue-i18n';
import { Plus } from 'lucide-vue-next';
import { computed } from 'vue';
import { onDelete } from './events';

export const menuActions = computed(() => [
    {
        value: {
            name: 'items.edit',
            params: {
                id: null,
            },
        },
        type: ActionType.Link,
        label: trans('item.button.edit'),
    },

    {
        value: '',
        type: ActionType.Event,
        label: trans('item.button.delete'),
        onClick: (data?: any) => onDelete(data), // Pass only the ID
    },
]);

export const toolbarActions = computed((): ToolbarAction[] => [
    {
        value: 'items.create',
        type: ActionType.Link,
        label: trans('item.button.add_new'),
        icon: Plus,
        onClick: (data?: any) => void {},
    },
]);
