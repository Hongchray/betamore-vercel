import { ActionType } from '@/enums/action_menu';
import { ToolbarAction } from '@/interfaces/ITable';
import { trans } from 'laravel-vue-i18n';
import { Plus } from 'lucide-vue-next';
import { computed } from 'vue';
import { onDelete } from './events';

export const menuActions = computed(() => [
    {
        value: {
            name: 'roles.edit',
            params: {
                id: null,
            },
        },
        type: ActionType.Link,
        label: trans('role.button.edit'),
    },
    {
        value: {
            name: 'roles.show',
            params: {
                id: null,
            },
        },
        type: ActionType.Link,
        label: trans('role.button.view'),
    },
    {
        value: '',
        type: ActionType.Event,
        label: trans('role.button.delete'),
        onClick: (data?: any) => onDelete(data),
    },
]);
export const toolbarActions = computed((): ToolbarAction[] => [
    {
        value: 'roles.create',
        type: ActionType.Link,
        label: trans('role.button.add_new'),
        icon: Plus,
        onClick: (data?: any) => void {},
    },
]);
