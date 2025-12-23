import { Badge } from '@/components/ui/badge';
import DataTableColumnHeader from '@/composables/ui/table/DataTableColumnHeader.vue';
import DataTableRowActions from '@/composables/ui/table/DataTableRowActions.vue';
import { formatCurrency } from '@/utils/formatCurrency';
import { formatDateTable } from '@/utils/formatDate';
import { paymentStatusColors } from '@/utils/statusColors';
import { Link } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { trans } from 'laravel-vue-i18n';
import { h } from 'vue';
import { menuActions } from './data';
import type { Payment } from './schema';

export const columns: ColumnDef<Payment>[] = [
    {
        accessorKey: 'order_id',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.order_number'),
            }),
        cell: ({ row }) => {
            const order = row.original.order;
            return h(
                Link, // <-- Use Inertia Link component
                {
                    href: `/orders/${order.id}`,
                    class: 'text-left font-medium text-blue-600 hover:underline',
                },
                () => order.order_number, // child content must be a function
            );
        },
    },
    {
        enableSorting: false,
        accessorKey: 'user_name',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.user_id'),
            }),
        cell: ({ row }) => {
            const user = row.original.order?.user;
            const fullName = user ? `${user.first_name} ${user.last_name}` : 'N/A';
            const email = user?.email || '';

            return h('div', { class: 'text-left' }, [
                h('div', { class: 'font-medium' }, fullName),
                h('div', { class: 'text-sm text-gray-500' }, email),
            ]);
        },
    },
    {
        enableSorting: false,
        accessorKey: 'payment_status',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.payment_status'),
            }),
        cell: ({ row }) => {
            const paymentStatus = row.original.payment_status as keyof typeof paymentStatusColors;
            const statusText = paymentStatus?.replace('_', ' ').replace(/\b\w/g, (l) => l.toUpperCase()) || 'Unknown';

            return h(
                Badge,
                {
                    class: `${paymentStatusColors[paymentStatus] || 'bg-gray-100'} w-fit`,
                    variant: 'outline',
                },
                statusText,
            );
        },
    },
    {
        enableSorting: false,
        accessorKey: 'payment_method_id',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.payment_method'),
            }),
        cell: ({ row }) => {
            const paymentMethod = row.original.payment_method;
            return h('div', { class: 'text-left' }, paymentMethod?.name || 'N/A');
        },
    },

    {
        enableSorting: false,
        accessorKey: 'order_delivery_fee',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.delivery_fee'),
            }),
        cell: ({ row }) =>
            h('div', { class: 'text-left' }, row.original.order?.delivery_fee !== null ? formatCurrency(row.original.order.delivery_fee) : '-'),
    },
    {
        enableSorting: false,
        accessorKey: 'order_total_price',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.total_price'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left font-semibold' }, formatCurrency(row.original.order?.total_price || 0)),
    },
    {
        enableSorting: false,
        accessorKey: 'order_total_amount',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.total_amount'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left font-semibold text-green-600' }, formatCurrency(row.original.order?.total_amount || 0)),
    },
    {
        enableSorting: false,
        accessorKey: 'order_note',
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
                    title: row.original.order?.note ?? '-',
                },
                row.original.order?.note ?? '-',
            ),
    },
    {
        enableSorting: false,
        accessorKey: 'created_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('order.table.payment_date'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left' }, formatDateTable(row.original.created_at)),
    },
    {
        enableSorting: false,
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
        enableSorting: false,
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
