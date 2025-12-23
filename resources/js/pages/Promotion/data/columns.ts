import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge'; // adjust path if needed
import DiscountValueDisplay from '@/composables/ui/display-value-discount/DiscountValueDisplay.vue';
import ImagePreview from '@/composables/ui/image-preview/ImagePreview.vue';
import DataTableColumnHeader from '@/composables/ui/table/DataTableColumnHeader.vue';
import DataTableRowActions from '@/composables/ui/table/DataTableRowActions.vue';
import { formatDateTable } from '@/utils/formatDate';
import { formatTime } from '@/utils/formatTime';
import { getInitials } from '@/utils/getInitials';
import { Link } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { trans } from 'laravel-vue-i18n';
import { h } from 'vue';
import { menuActions } from './data';
import type { Promotion } from './schema';
export const columns: ColumnDef<Promotion>[] = [
    // {
    //     id: 'select',
    //     header: ({ table }) =>
    //         h(Checkbox, {
    //             modelValue: table.getIsAllPageRowsSelected(),
    //             'onUpdate:modelValue': (value: boolean | 'indeterminate') => table.toggleAllPageRowsSelected(!!value),
    //             ariaLabel: 'Select all',
    //         }),
    //     cell: ({ row }) =>
    //         h(Checkbox, {
    //             modelValue: row.getIsSelected(),
    //             'onUpdate:modelValue': (value: boolean | 'indeterminate') => row.toggleSelected(!!value),
    //             ariaLabel: 'Select row',
    //         }),
    //     enableSorting: false,
    // },
    {
        accessorKey: 'promotion_id',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('promotion.table.id'),
            }),
        cell: ({ row }) => {
            const data = row.original;
            return h(
                Link,
                {
                    href: `/promotions/${data.id}/edit`,
                    class: 'text-left font-medium text-blue-600 hover:underline',
                },
                () => data.promotion_id,
            );
        },
    },
    {
        accessorKey: 'banner',
        enableSorting: false,
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('banner.table.banner_image'),
            }),
        cell: ({ row }) => {
            const logoUrl = String(row.getValue('banner'));
            const firstName = String(row.getValue('name_en'));

            return h('div', { class: 'flex space-x-2' }, [
                h('div', { class: 'h-12 w-20 relative' }, [
                    logoUrl && logoUrl !== 'null' && logoUrl !== ''
                        ? h(ImagePreview, {
                              image: logoUrl,
                              className: 'h-auto w-auto rounded-md object-cover cursor-zoom-in',
                          })
                        : h(
                              Avatar,
                              { class: 'h-full w-full rounded-md' },
                              {
                                  default: () => [h(AvatarFallback, () => getInitials(firstName))],
                              },
                          ),
                ]),
            ]);
        },
    },
    {
        accessorKey: 'name_en',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('promotion.table.name') + ' (EN)',
            }),
        cell: ({ row }) => {
            return h('div', { class: 'text-left font-medium' }, row.original.name_en);
        },
    },
    {
        accessorKey: 'name_km',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('promotion.table.name') + ' (KM)',
            }),
        cell: ({ row }) => {
            return h('div', { class: 'text-left font-medium' }, row.original.name_km);
        },
    },

    {
        accessorKey: 'description_en',
        enableSorting: false,
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('promotion.table.description') + ' (EN)',
            }),
        cell: ({ row }) => {
            const description = row.getValue('description_en') as string;
            return h('div', { class: 'text-left' }, description || '-');
        },
    },
    {
        accessorKey: 'description_km',
        enableSorting: false,
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('promotion.table.description') + ' (km)',
            }),
        cell: ({ row }) => {
            const description = row.getValue('description_km') as string;
            return h('div', { class: 'text-left' }, description || '-');
        },
    },
    {
        accessorKey: 'type',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('promotion.table.type'),
            }),
        cell: ({ row }) => {
            const type = row.getValue('type') as 'percent' | 'flat';
            return h('div', { class: 'capitalize' }, trans(`promotion.form.type.${type}`));
        },
    },
    {
        accessorKey: 'discount_value',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('promotion.table.discount_value'),
            }),
        cell: ({ row }) => {
            const value = row.getValue('discount_value') as number;
            const type = row.getValue('type') as 'percent' | 'flat';

            return h(DiscountValueDisplay, { value, type });
        },
    },

    {
        accessorKey: 'status',
        enableSorting: false,
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('promotion.table.status'),
            }),
        cell: ({ row }) => {
            const status = (row.getValue('status') as string) || '-';

            const statusClassMap: Record<string, string> = {
                active: 'bg-green-100 text-green-800 pt-1',
                upcoming: 'bg-yellow-100 text-yellow-800 pt-1',
                expired: 'bg-red-100 text-red-800 pt-1',
                default: 'bg-gray-100 text-gray-800 pt-1',
            };

            const badgeClass = statusClassMap[status] || statusClassMap.default;

            return h(Badge, { class: `capitalize ${badgeClass}` }, () => status);
        },
    },

    {
        accessorKey: 'start_date',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('promotion.table.start_date'),
            }),
        cell: ({ row }) => {
            const startDate = row.getValue('start_date') as string | null;
            return h('div', { class: 'text-left' }, startDate ? formatDateTable(startDate) : '-');
        },
    },
    {
        accessorKey: 'end_date',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('promotion.table.end_date'),
            }),
        cell: ({ row }) => {
            const endDate = row.getValue('end_date') as string | null;
            return h('div', { class: 'text-left' }, endDate ? formatDateTable(endDate) : '-');
        },
    },

    {
        accessorKey: 'start_time',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('promotion.table.start_time'),
            }),
        cell: ({ row }) => {
            const start_time = row.getValue('start_time') as string | null;

            // Format in 12-hour or 24-hour mode
            const formatted = start_time ? formatTime(start_time, '12h') : '-';

            return h('div', { class: 'text-left' }, formatted);
        },
    },

    {
        accessorKey: 'end_time',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('promotion.table.end_time'),
            }),
        cell: ({ row }) => {
            const end_time = row.getValue('end_time') as string | null;

            // 👇 Choose '12h' or '24h' mode
            const formatted = end_time ? formatTime(end_time, '12h') : '-';

            return h('div', { class: 'text-left' }, formatted);
        },
    },

    {
        accessorKey: 'created_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('promotion.table.created_at'),
            }),
        cell: ({ row }) => {
            const createdAt = row.getValue('created_at') as string;
            return h('div', { class: 'text-left' }, formatDateTable(createdAt));
        },
    },
    {
        accessorKey: 'updated_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('promotion.table.updated_at'),
            }),
        cell: ({ row }) => {
            const updatedAt = row.getValue('updated_at') as string;
            return h('div', { class: 'text-left' }, formatDateTable(updatedAt));
        },
    },
    {
        id: 'actions',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('promotion.table.action'),
            }),
        cell: ({ row }) =>
            h(DataTableRowActions, {
                row,
                menuActions: menuActions.value,
            }),
    },
];
