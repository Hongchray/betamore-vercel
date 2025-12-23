import { Badge } from '@/components/ui/badge';
import DataTableColumnHeader from '@/composables/ui/table/DataTableColumnHeader.vue';
import DataTableRowActions from '@/composables/ui/table/DataTableRowActions.vue';
import { formatCurrency } from '@/utils/formatCurrency';
import { formatDateTable } from '@/utils/formatDate';
import { orderStatusColors, paymentStatusColors } from '@/utils/statusColors';
import { Link } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { trans } from 'laravel-vue-i18n';
import { h } from 'vue';
import { menuActions } from './data';
import type { Order } from './schema';
export const columns: ColumnDef<Order>[] = [
    {
        accessorKey: 'order_number',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.order_number'),
            }),
        cell: ({ row }) => {
            const order = row.original;
            return h(
                Link,
                {
                    href: `/orders/${order.id}`,
                    class: 'text-left font-medium text-blue-600 hover:underline',
                },
                () => order.order_number,
            );
        },
    },

    {
        accessorKey: 'user_id',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.user_id'),
            }),
        cell: ({ row }) => {
            const user = row.original?.user;
            const fullName = user ? `${user.first_name} ${user.last_name}` : 'N/A';
            const email = user?.email || '';

            return h('div', { class: 'text-left' }, [
                h('div', { class: 'font-medium' }, fullName),
                h('div', { class: 'text-sm text-gray-500' }, email),
            ]);
        },
    },

    {
        accessorKey: 'delivery_id',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.delivery_id'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left' }, row.original.delivery?.name ?? '-'),
    },

    {
        accessorKey: 'status',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.status'),
            }),
        cell: ({ row }) => {
            const status = row.original.status as keyof typeof orderStatusColors;
            const statusText = status.charAt(0).toUpperCase() + status.slice(1);

            return h(
                Badge,
                {
                    class: `${orderStatusColors[status]} w-fit pt-1`,
                    variant: 'outline',
                },
                statusText,
            );
        },
    },

    {
        accessorKey: 'payment_method_id',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.payment_method'),
            }),
        cell: ({ row }) => {
            return h('div', { class: 'text-left' }, row.original.order_payment.payment_method.name);
        },
    },

    {
        accessorKey: 'payment_status',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.payment_status'),
            }),
        cell: ({ row }) => {
            const status = row.original.order_payment.payment_status as unknown as keyof typeof paymentStatusColors;
            const statusText = status.charAt(0).toUpperCase() + status.slice(1);

            return h(
                Badge,
                {
                    class: `${paymentStatusColors[status]} w-fit pt-1`,
                    variant: 'outline',
                },
                statusText,
            );
        },
    },
    {
        accessorKey: 'delivery_fee',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.delivery_fee'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left' }, row.original.delivery_fee !== null ? formatCurrency(row.original.delivery_fee) : '-'),
    },

    {
        accessorKey: 'total_price',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.total_price'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left font-semibold' }, formatCurrency(row.original.total_price)),
    },

    {
        accessorKey: 'total_amount',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.total_amount'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left font-semibold' }, formatCurrency(row.original.total_amount)),
    },

    {
        accessorKey: 'note',
        enableSorting: false,
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.note'),
            }),
        cell: ({ row }) =>
            h(
                'div',
                {
                    class: 'text-left truncate max-w-[200px]',
                    title: row.original.note ?? '-',
                },
                row.original.note ?? '-',
            ),
    },

    {
        accessorKey: 'created_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.created_at'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left' }, formatDateTable(row.original.created_at)),
    },

    {
        accessorKey: 'updated_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.updated_at'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left' }, formatDateTable(row.original.updated_at)),
    },

    {
        id: 'actions',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.action'),
            }),
        cell: ({ row }) =>
            h(DataTableRowActions, {
                row,
                menuActions: menuActions.value,
            }),
    },
];
