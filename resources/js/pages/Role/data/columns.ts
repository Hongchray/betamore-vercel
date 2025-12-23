import DataTableColumnHeader from '@/composables/ui/table/DataTableColumnHeader.vue';
import DataTableRowActions from '@/composables/ui/table/DataTableRowActions.vue';
import { formatDateTable } from '@/utils/formatDate';
import type { ColumnDef } from '@tanstack/vue-table';
import { trans } from 'laravel-vue-i18n';
import { h } from 'vue';
import { menuActions } from './data';
import type { Role } from './schema';

export const columns: ColumnDef<Role>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('role.table.name'),
            }),
        cell: ({ row }) => {
            const name = row.getValue('name') as string;
            return h('div', { class: 'text-left' }, name);
        },
    },
    {
        accessorKey: 'guard_name',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('role.table.guard_name'),
            }),
        cell: ({ row }) => {
            const guard_name = row.getValue('guard_name') as string;
            return h('div', { class: 'text-left' }, guard_name);
        },
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
        id: 'actions',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column: column,
                title: trans('role.table.action'),
            }),
        cell: ({ row }) =>
            h(DataTableRowActions, {
                row,
                menuActions: menuActions.value,
            }),
    },
];
