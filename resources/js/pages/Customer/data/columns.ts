//Users/apple/Documents/Focuz Solution/betamore-admin/resources/js/pages/Customer/data/columns.ts
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
import type { User } from './schema';
export const columns: ColumnDef<User>[] = [
    {
        accessorKey: 'user_id',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('user.table.user_id'),
            }),
        cell: ({ row }) => {
            const data = row.original;
            return h(
                Link,
                {
                    href: `/customers/${data.id}`,
                    class: 'text-left font-medium text-blue-600 hover:underline',
                },
                () => data.user_id,
            );
        },
    },
    {
        accessorKey: 'first_name',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('user.table.name'),
            }),
        cell: ({ row }) => {
            const first_name = row.original.first_name;
            const last_name = row.original.last_name;

            return h('div', { class: 'text-left font-medium' }, `${first_name} ${last_name}`);
        },
    },
    {
        accessorKey: 'image',
        enableSorting: false,
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: trans('user.table.image'),
            }),
        cell: ({ row }) => {
            const logoUrl = String(row.getValue('image'));
            const firstName = String(row.getValue('first_name'));

            return h('div', { class: 'flex space-x-2' }, [
                h('div', { class: 'h-12 w-12 relative' }, [
                    // If image exists, use ImagePreview component
                    logoUrl && logoUrl !== 'null' && logoUrl !== ''
                        ? h(ImagePreview, {
                              image: logoUrl,
                              className: 'h-12 w-12 rounded-md object-cover cursor-zoom-in',
                          })
                        : // Fallback to Avatar with initials if no image
                          h(
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
        accessorKey: 'gender',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('user.table.gender'),
            }),
        cell: ({ row }) => {
            const gender = row.getValue('gender');
            return h('div', { class: 'capitalize' }, trans(`user.form.gender.${gender}`));
        },
    },

    {
        accessorKey: 'phone',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('user.table.phone'),
            }),
        cell: ({ row }) => {
            const phone = row.getValue('phone') as string;
            return h('div', { class: 'text-left font-medium' }, phone);
        },
    },
    {
        accessorKey: 'email',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('user.table.email'),
            }),
        cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('email')),
    },
    {
        accessorKey: 'telegram',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('user.table.telegram'),
            }),
        cell: ({ row }) => h('div', { class: '' }, row.getValue('telegram')),
    },

    {
        accessorKey: 'created_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('user.table.created_at'),
            }),
        cell: ({ row }) => {
            const updatedAt = row.getValue('created_at') as string;

            // Method 1: Use the enhanced formatDate with translations
            const formatted = formatDateTable(updatedAt);

            return h('div', { class: 'text-left' }, formatted);
        },
    },

    {
        accessorKey: 'updated_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('user.table.updated_at'),
            }),
        cell: ({ row }) => {
            const updatedAt = row.getValue('updated_at') as string;

            // Method 1: Use the enhanced formatDate with translations
            const formatted = formatDateTable(updatedAt);

            return h('div', { class: 'text-left' }, formatted);
        },
    },
    {
        accessorKey: 'deleted_at',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('user.table.deleted_at'),
            }),
        cell: ({ row }) => {
            const deletedAt = row.getValue('deleted_at') as string | null;

            if (!deletedAt) {
                return h('div', { class: 'text-left' }, '-');
            }

            const formatted = formatDateTable(deletedAt);

            return h('div', { class: 'text-left text-red-500 font-medium' }, formatted);
        },
    },

    {
        id: 'actions',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('user.table.action'),
            }),
        cell: ({ row }) =>
            h(DataTableRowActions, {
                row,
                menuActions: menuActions.value,
            }),
    },
];
