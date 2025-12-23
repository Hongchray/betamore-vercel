import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import ImagePreview from '@/composables/ui/image-preview/ImagePreview.vue';
import DataTableColumnHeader from '@/composables/ui/table/DataTableColumnHeader.vue';
import DataTableRowActions from '@/composables/ui/table/DataTableRowActions.vue';
import { formatDateTable } from '@/utils/formatDate';
import { getInitials } from '@/utils/getInitials';
import { Link } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { trans } from 'laravel-vue-i18n';
import { h } from 'vue';
import { menuActions } from './data';
import type { Company } from './schema'; // Make sure to update this type
export const columns: ColumnDef<Company>[] = [
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
        accessorKey: 'company_id',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('company.table.company_id'),
            }),
        cell: ({ row }) => {
            const data = row.original;
            return h(
                Link,
                {
                    href: `/companies/${data.id}/edit`,
                    class: 'text-left font-medium text-blue-600 hover:underline',
                },
                () => data.company_id,
            );
        },
    },
    {
        accessorKey: 'name_en',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('company.table.name') + ' (EN)',
            }),
        cell: ({ row }) => h('div', { class: 'text-left font-medium' }, row.original.name_en),
    },

    {
        accessorKey: 'name_km',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('company.table.name') + ' (KM)',
            }),
        cell: ({ row }) => h('div', { class: 'text-left font-medium' }, row.original.name_km),
    },
    {
        accessorKey: 'logo',
        enableSorting: false,
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('company.table.logo'),
            }),
        cell: ({ row }) => {
            const logoUrl = String(row.getValue('logo'));
            const name = String(row.getValue('name_en'));

            return h('div', { class: 'flex space-x-2' }, [
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
        accessorKey: 'description_en',
        enableSorting: false,
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('company.table.description') + ' (EN)',
            }),
        cell: ({ row }) =>
            h(
                'div',
                {
                    class: 'text-left font-medium truncate max-w-[300px] block',
                    title: row.getValue('description_en') as string, // shows full text on hover
                },
                row.getValue('description_en') as string,
            ),
    },
    {
        accessorKey: 'description_km',
        enableSorting: false,
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('company.table.description') + ' (KM)',
            }),
        cell: ({ row }) =>
            h(
                'div',
                {
                    class: 'text-left font-medium truncate max-w-[300px] block',
                    title: row.getValue('description_en') as string, // shows full text on hover
                },
                row.getValue('description_km') as string,
            ),
    },
    {
        accessorKey: 'created_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('company.table.created_at'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left' }, formatDateTable(row.getValue('created_at') as string)),
    },
    {
        accessorKey: 'updated_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('company.table.updated_at'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left' }, formatDateTable(row.getValue('updated_at') as string)),
    },
    {
        id: 'actions',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('company.table.action'),
            }),
        cell: ({ row }) =>
            h(DataTableRowActions, {
                row,
                menuActions: menuActions.value,
            }),
    },
];
