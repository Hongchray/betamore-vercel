import { ActionType } from '@/enums/action_menu';
import { ToolbarAction } from '@/interfaces/ITable';
import { trans } from 'laravel-vue-i18n';
import { Plus } from 'lucide-vue-next';
import { computed } from 'vue';
import { onDelete } from './events';

export const menuActions = computed(() => [
    {
        value: {
            name: 'deliveries.edit',
            params: {
                id: null,
            },
        },
        type: ActionType.Link,
        label: trans('delivery.button.edit'),
    },
    {
        value: {
            name: 'deliveries.show',
            params: {
                id: null,
            },
        },
        type: ActionType.Link,
        label: trans('delivery.button.view'),
    },
    {
        value: '',
        type: ActionType.Event,
        label: trans('delivery.button.delete'),
        onClick: (data?: any) => onDelete(data),
    },
]);

export const toolbarActions = computed((): ToolbarAction[] => [
    {
        value: 'deliveries.create',
        type: ActionType.Link,
        label: trans('delivery.button.add_new'),
        icon: Plus,
        onClick: (data?: any) => void {},
    },
]);

export function getDeliveryBreadcrumbs() {
    return {
        index: computed(() => [
            {
                title: trans('delivery.breadcrumb.index'),
                href: '/deliveries',
            },
        ]),

        show: (deliveryId: string | number) =>
            computed(() => [
                {
                    title: trans('delivery.breadcrumb.index'),
                    href: '/deliveries',
                },
                {
                    title: trans('delivery.breadcrumb.show'),
                    href: `/deliveries/${deliveryId}`,
                },
            ]),

        create: computed(() => [
            {
                title: trans('delivery.breadcrumb.index'),
                href: '/deliveries',
            },
            {
                title: trans('delivery.breadcrumb.create'),
                href: '/deliveries/create',
            },
        ]),

        edit: (deliveryId: string | number) =>
            computed(() => [
                {
                    title: trans('delivery.breadcrumb.index'),
                    href: '/deliveries',
                },
                {
                    title: trans('delivery.breadcrumb.edit'),
                    href: `/deliveries/${deliveryId}/edit`,
                },
            ]),
    };
}
