import DataTableColumnHeader from '@/composables/ui/table/DataTableColumnHeader.vue';
import { formatCurrency } from '@/utils/formatCurrency';
import type { ColumnDef } from '@tanstack/vue-table';
import { trans } from 'laravel-vue-i18n';
import { h } from 'vue';
import type { Report } from './schema';

export const columns: ColumnDef<Report>[] = [
    {
        accessorKey: 'item_id',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('report.table.item_id'), // Optional: You can add this key in your lang file
            }),
        cell: ({ row }) =>
            h(
                'a',
                {
                    href: `/items/${row.original.id}/edit`,
                    class: 'text-left font-medium text-blue-600 hover:underline',
                },
                row.original.item_id,
            ),
    },

    {
        accessorKey: 'item_name_en',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('report.table.item_name_en'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left font-medium' }, row.original.item_name_en),
    },
    {
        accessorKey: 'item_name_km',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('report.table.item_name_km'),
            }),
        cell: ({ row }) => {
            console.log(row.original);
            return h('div', { class: 'text-left' }, row.original.item_name_km);
        },
    },
    {
        accessorKey: 'modification_name',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('report.table.modification_name'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left' }, row.original.modification_name),
    },
    {
        accessorKey: 'unit',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('report.table.unit'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left' }, row.original.unit),
    },
    {
        accessorKey: 'unit_price',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('report.table.unit_price'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left font-semibold' }, formatCurrency(parseFloat(row.original.unit_price))),
    },
    {
        accessorKey: 'order_count',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('report.table.order_count'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left' }, row.original.order_count),
    },
    {
        accessorKey: 'total_quantity_ordered',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('report.table.total_quantity_ordered'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left font-medium' }, row.original.total_quantity_ordered),
    },
    {
        accessorKey: 'total_revenue',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('report.table.total_revenue'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left font-bold text-green-700' }, formatCurrency(parseFloat(row.original.total_revenue))),
    },
];
