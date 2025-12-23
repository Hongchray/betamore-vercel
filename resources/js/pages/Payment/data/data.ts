import { ActionType } from '@/enums/action_menu';
import { PaymentStatus } from '@/enums/PaymentStatus';
import { ToolbarAction } from '@/interfaces/ITable';
import { trans } from 'laravel-vue-i18n';
import { CheckCircle, Clock, RefreshCcw, Slash, XCircle } from 'lucide-vue-next';
import { computed } from 'vue';

export const menuActions = computed(() => [
    {
        value: {
            name: 'payments.show',
            params: {
                order: null,
            },
        },
        type: ActionType.Link,
        label: trans('order.button.view'),
    },
]);

export const toolbarActions = computed((): ToolbarAction[] => [
    // {
    //     value: 'orders.create',
    //     type: ActionType.Link,
    //     label: trans('order.button.add_new'),
    //     icon: Plus,
    //     onClick: (data?: any) => void {}, // Keep empty or remove if unused
    // },
]);
export const payment_status = computed(() => [
    {
        value: PaymentStatus.Pending,
        label: 'Pending',
        icon: Clock,
    },
    {
        value: PaymentStatus.Approved,
        label: 'Approved',
        icon: CheckCircle,
    },
    {
        value: PaymentStatus.Declined,
        label: 'Declined',
        icon: XCircle,
    },
    {
        value: PaymentStatus.Refunded,
        label: 'Refunded',
        icon: RefreshCcw, // optional icon for refund
    },
    {
        value: PaymentStatus.Cancelled,
        label: 'Cancelled',
        icon: Slash, // optional icon for cancel
    },
]);
export function getOrderBreadcrumbs() {
    return {
        index: computed(() => [
            {
                title: trans('order.breadcrumb.index'),
                href: '/orders',
            },
        ]),

        show: (orderId: string | number) =>
            computed(() => [
                {
                    title: trans('order.breadcrumb.index'),
                    href: '/orders',
                },
                {
                    title: trans('order.breadcrumb.show'),
                    href: `/orders/${orderId}`,
                },
            ]),

        create: computed(() => [
            {
                title: trans('order.breadcrumb.index'),
                href: '/orders',
            },
            {
                title: trans('order.breadcrumb.create'),
                href: '/orders/create',
            },
        ]),

        edit: (orderId: string | number) =>
            computed(() => [
                {
                    title: trans('order.breadcrumb.index'),
                    href: '/orders',
                },
                {
                    title: trans('order.breadcrumb.edit'),
                    href: `/orders/${orderId}/edit`,
                },
            ]),
    };
}
