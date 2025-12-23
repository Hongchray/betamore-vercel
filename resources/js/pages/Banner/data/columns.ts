import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import ImagePreview from '@/composables/ui/image-preview/ImagePreview.vue'; // Add this import
import DataTableColumnHeader from '@/composables/ui/table/DataTableColumnHeader.vue';
import DataTableRowActions from '@/composables/ui/table/DataTableRowActions.vue';
import { formatDateTable } from '@/utils/formatDate';
import { getInitials } from '@/utils/getInitials';
import { Link } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { trans } from 'laravel-vue-i18n';
import { h } from 'vue';
import { menuActions } from './data';
import type { Banner } from './schema';
export const columns: ColumnDef<Banner>[] = [
    {
        accessorKey: 'banner_id',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('banner.table.banner_id'),
            }),
        cell: ({ row }) => {
            const data = row.original;
            return h(
                Link,
                {
                    href: `/banners/${data.id}/edit`,
                    class: 'text-left font-medium text-blue-600 hover:underline',
                },
                () => data.banner_id,
            );
        },
    },
    {
        accessorKey: 'name',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('banner.table.name'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left font-medium' }, row.original.name),
    },
    {
        accessorKey: 'banner_image',
        enableSorting: false,
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('banner.table.banner_image'),
            }),
        cell: ({ row }) => {
            const logoUrl = String(row.getValue('banner_image'));
            const firstName = String(row.getValue('name'));

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
        accessorKey: 'description',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('banner.table.description'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left font-medium' }, row.getValue('description') as string),
    },
    {
        accessorKey: 'created_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('banner.table.created_at'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left' }, formatDateTable(row.getValue('created_at') as string)),
    },
    {
        accessorKey: 'updated_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('banner.table.updated_at'),
            }),
        cell: ({ row }) => h('div', { class: 'text-left' }, formatDateTable(row.getValue('updated_at') as string)),
    },
    {
        id: 'actions',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('banner.table.action'),
            }),
        cell: ({ row }) =>
            h(DataTableRowActions, {
                row,
                menuActions: menuActions.value,
            }),
    },
];
