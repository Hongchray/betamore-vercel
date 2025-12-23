import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import ImagePreview from '@/composables/ui/image-preview/ImagePreview.vue';
import DataTableColumnHeader from '@/composables/ui/table/DataTableColumnHeader.vue';
import DataTableRowActions from '@/composables/ui/table/DataTableRowActions.vue';
import { formatCurrency } from '@/utils/formatCurrency';
import { formatDateTable } from '@/utils/formatDate';
import { getInitials } from '@/utils/getInitials';
import { Link } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { trans } from 'laravel-vue-i18n';
import { h } from 'vue';
import { menuActions } from './data';
import type { Delivery } from './schema';
export const columns: ColumnDef<Delivery>[] = [
    {
        accessorKey: 'delivery_id',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('delivery.table.delivery_id'),
            }),
        cell: ({ row }) => {
            const data = row.original;
            return h(
                Link,
                {
                    href: `/deliveries/${data.id}`,
                    class: 'text-left font-medium text-blue-600 hover:underline',
                },
                () => data.delivery_id,
            );
        },
    },

    {
        accessorKey: 'image',
        enableSorting: false,
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('delivery.table.image'),
            }),
        cell: ({ row }) => {
            const logoUrl = String(row.getValue('image'));
            const name = String(row.getValue('name'));

            return h('div', { class: 'flex justify-start' }, [
                h('div', { class: 'h-12 w-12 relative' }, [
                    logoUrl && logoUrl !== 'null' && logoUrl !== ''
                        ? h(ImagePreview, {
                              image: logoUrl,
                              className: 'h-12 w-12 rounded-md object-cover cursor-zoom-in',
                          })
                        : h(
                              Avatar,
                              { class: 'h-full w-full rounded-md' },
                              {
                                  default: () => [h(AvatarFallback, () => getInitials(name))],
                              },
                          ),
                ]),
            ]);
        },
    },

    {
        accessorKey: 'name',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('delivery.table.name'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left font-medium' }, row.getValue('name')),
    },

    {
        accessorKey: 'shipping_fee',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('delivery.table.shipping_fee'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left' }, formatCurrency(row.original.shipping_fee)),
    },

    {
        accessorKey: 'description',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('delivery.table.description'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left' }, row.getValue('description') ?? '-'),
    },
    {
        accessorKey: 'is_active',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('delivery.table.is_active'),
            }),
        cell: ({ row }) => {
            const isActive = row.getValue('is_active');
            const statusText = isActive ? trans('delivery.status.active') : trans('delivery.status.inactive');
            const statusClass = isActive ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200';

            return h(
                'div',
                { class: 'text-left' },
                h(
                    'span',
                    {
                        class: `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${statusClass}`,
                    },
                    [
                        h('span', {
                            class: `w-1.5 h-1.5 rounded-full mr-1.5 ${isActive ? 'bg-green-400' : 'bg-red-400'}`,
                        }),
                        statusText,
                    ],
                ),
            );
        },
    },

    {
        accessorKey: 'created_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('delivery.table.created_at'),
            }),
        cell: ({ row }) => {
            const value = row.getValue('created_at') as string;
            return h('div', { class: 'text-left' }, formatDateTable(value));
        },
    },

    {
        accessorKey: 'updated_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('delivery.table.updated_at'),
            }),
        cell: ({ row }) => {
            const value = row.getValue('updated_at') as string;
            return h('div', { class: 'text-left' }, formatDateTable(value));
        },
    },

    {
        id: 'actions',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('delivery.table.action'),
            }),
        cell: ({ row }) =>
            h(DataTableRowActions, {
                row,
                menuActions: menuActions.value,
            }),
    },
];
