import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import ImagePreview from '@/composables/ui/image-preview/ImagePreview.vue';
import DataTableColumnHeader from '@/composables/ui/table/DataTableColumnHeader.vue';
import DataTableRowActions from '@/composables/ui/table/DataTableRowActions.vue';
import { getInitials } from '@/composables/useInitials';
import { formatDateTable } from '@/utils/formatDate';
import { Link } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { trans } from 'laravel-vue-i18n';
import { h } from 'vue';
import { menuActions } from './data';
import type { Item } from './schema';
export const columns: ColumnDef<Item>[] = [
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
    // },
    {
        accessorKey: 'item_id',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('item.table.item_id'),
            }),
        cell: ({ row }) => {
            const data = row.original;
            return h(
                Link,
                {
                    href: `/items/${data.id}/edit`,
                    class: 'text-left font-medium text-blue-600 hover:underline',
                },
                () => data.item_id,
            );
        },
    },
    {
        accessorKey: 'image',
        enableSorting: false,
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('item.table.image'),
            }),
        cell: ({ row }) => {
            const images = row.original.images;

            // Find the image where is_main = 1, fallback to first image if not found
            const mainImageObj = Array.isArray(images) ? images.find((img) => img.is_main === 1) || images[0] : null;

            const logoUrl = mainImageObj?.image ? String(mainImageObj.image) : null;
            const name = String(row.getValue('name_en'));

            return h('div', { class: 'flex space-x-2' }, [
                h('div', { class: 'h-12 w-12 relative' }, [
                    logoUrl
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
        accessorKey: 'name_en',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('item.table.name') + ' (EN)',
            }),
        cell: ({ row }) => {
            const name = row.getValue('name_en') as string;
            return h('div', { class: 'text-left' }, name);
        },
    },
    {
        accessorKey: 'name_km',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('item.table.name') + ' (KM)',
            }),
        cell: ({ row }) => {
            const name = row.getValue('name_km') as string;
            return h('div', { class: 'text-left' }, name);
        },
    },
    {
        accessorKey: 'company_id',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('item.table.company_id'),
            }),
        cell: ({ row }) => {
            const company = row.original.company;
            const logoUrl = company?.logo;
            const companyName = company?.name_en?.trim() ? company.name_en : 'N/A';

            return h('div', { class: 'flex items-center space-x-3' }, [h('div', { class: 'text-md' }, companyName)]);
        },
    },
    {
        accessorKey: 'description_en',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('item.table.description') + ' (EN)',
            }),
        cell: ({ row }) =>
            h(
                'div',
                {
                    class: 'text-left font-medium truncate max-w-[250px] block',
                    title: row.getValue('description_en') as string,
                },
                row.getValue('description_en') as string,
            ),
    },
    {
        accessorKey: 'description_km',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('item.table.description') + ' (KM)',
            }),
        cell: ({ row }) =>
            h(
                'div',
                {
                    class: 'text-left font-medium truncate max-w-[250px] block',
                    title: row.getValue('description_km') as string,
                },
                row.getValue('description_en') as string,
            ),
    },
    {
        accessorKey: 'created_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('item.table.created_at'),
            }),
        cell: ({ row }) => {
            const createdAt = row.getValue('created_at') as string;
            const formatted = formatDateTable(createdAt);
            return h('div', { class: 'text-left' }, formatted);
        },
    },
    {
        accessorKey: 'updated_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('item.table.updated_at'),
            }),
        cell: ({ row }) => {
            const updatedAt = row.getValue('updated_at') as string;
            const formatted = formatDateTable(updatedAt);
            return h('div', { class: 'text-left' }, formatted);
        },
    },
    {
        id: 'actions',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('item.table.action'),
            }),
        cell: ({ row }) =>
            h(DataTableRowActions, {
                row,
                menuActions: menuActions.value,
            }),
    },
];
