import { ActionType } from '@/enums/action_menu';
import { DeliveryStatus } from '@/enums/DeliveryStatus';
import { OrderStatus } from '@/enums/OrderStatus';
import { PaymentStatus } from '@/enums/PaymentStatus';
import { ToolbarAction } from '@/interfaces/ITable';
import { trans } from 'laravel-vue-i18n';
import { CheckCircle, Circle, Clock, Loader, MoveRight, PackageCheck, RefreshCcw, Truck, XCircle, XCircleIcon } from 'lucide-vue-next';
import { computed } from 'vue';

export const order_status = computed(() => [
    {
        value: OrderStatus.PENDING,
        label: 'Pending',
        icon: Clock,
    },
    {
        value: OrderStatus.CONFIRMED,
        label: 'Confirmed',
        icon: CheckCircle,
    },
    {
        value: OrderStatus.PROCESSING,
        label: 'Processing',
        icon: Loader,
    },
    {
        value: OrderStatus.SHIPPED,
        label: 'Shipped',
        icon: Truck,
    },
    {
        value: OrderStatus.DELIVERED,
        label: 'Delivered',
        icon: PackageCheck,
    },
    {
        value: OrderStatus.CANCELLED,
        label: 'Cancelled',
        icon: XCircle,
    },
]);

export const delivery_status = computed(() => [
    {
        value: DeliveryStatus.PENDING,
        label: 'Pending',
        icon: Circle,
    },
    {
        value: DeliveryStatus.PICKED_UP,
        label: 'Picked Up',
        icon: Truck,
    },
    {
        value: DeliveryStatus.IN_TRANSIT,
        label: 'In Transit',
        icon: MoveRight,
    },
    {
        value: DeliveryStatus.DELIVERED,
        label: 'Delivered',
        icon: CheckCircle,
    },
    {
        value: DeliveryStatus.FAILED,
        label: 'Failed',
        icon: XCircle,
    },
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
        icon: RefreshCcw,
    },
    {
        value: PaymentStatus.Cancelled,
        label: 'Cancelled',
        icon: XCircleIcon,
    },
]);

export const menuActions = computed(() => [
    {
        value: {
            name: 'orders.show',
            params: {
                id: null,
            },
        },
        type: ActionType.Link,
        label: trans('order.button.view'),
    },
]);

export const toolbarActions = computed((): ToolbarAction[] => [
    // Uncomment and modify as needed
    // {
    //     value: 'orders.create',
    //     type: ActionType.Link,
    //     label: trans('order.button.add_new'),
    //     icon: Plus,
    //     onClick: (data?: any) => {}, // Keep empty or remove if unused
    // },
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
