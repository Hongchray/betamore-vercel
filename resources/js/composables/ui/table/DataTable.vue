<script setup lang="ts">
import type {
    ColumnDef,
    ColumnFiltersState,
    ColumnPinningState,
    PaginationState,
    SortingState,
    Table as TTable,
    VisibilityState,
} from '@tanstack/vue-table';
import {
    FlexRender,
    getCoreRowModel,
    getFacetedRowModel,
    getFacetedUniqueValues,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table';

import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { PageData, TableInfo, ToolbarAction } from '@/interfaces/ITable';
import { valueUpdater } from '@/lib/utils';
import { router } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import DataTablePagination from './DataTablePagination.vue';
import DataTableToolbar from './DataTableToolbar.vue';

interface DataTableProps<T> {
    columns: ColumnDef<T, any>[];
    data: PageData<T>;
    tableInfo: TableInfo;
}

const props = defineProps<DataTableProps<any>>();
const pagination = ref<PaginationState>({
    pageIndex: props.data.current_page - 1,
    pageSize: props.data.per_page,
});
const sorting = ref<SortingState>([]);
const columnFilters = ref<ColumnFiltersState>(props.data.filter);
const columnVisibility = ref<VisibilityState>({});
const rowSelection = ref({});

// Dynamic column pinning state
const columnPinning = ref<ColumnPinningState>({
    left: props.tableInfo?.pinned_left || [],
    right: props.tableInfo?.pinned_right || [],
});

// Helper function to get base params
const getBaseParams = () => {
    let filters = {};
    if (columnFilters.value) {
        filters = columnFilters.value.reduce((acc: any, filter) => {
            acc[filter.id] = filter.value;
            return acc;
        }, {});
    }

    return {
        page: pagination.value.pageIndex + 1,
        per_page: pagination.value.pageSize,
        table_id: props.tableInfo?.table_id,
        sort_field: sorting.value[0]?.id,
        sort_direction: sorting.value.length == 0 ? undefined : sorting.value[0]?.desc ? 'desc' : 'asc',
        ...(props.data.search ? { search: props.data.search } : {}),
        ...(props.data.filter_type ? { filter_type: props.data.filter_type } : {}),
        ...(props.data.filter_order_status ? { filter_order_status: props.data.filter_order_status } : {}),
        ...filters,
    };
};

// Helper function to make route request
const makeRequest = (params: any) => {
    router.get(
        typeof props.tableInfo.route === 'string' ? route(props.tableInfo.route) : route(props.tableInfo.route?.name, props.tableInfo.route?.params),
        params,
        { preserveState: true, preserveScroll: true },
    );
};

const table = useVueTable({
    get data() {
        return props.data.data;
    },
    get columns() {
        return props.columns;
    },
    debugTable: false,
    state: {
        get sorting() {
            return sorting.value;
        },
        get columnFilters() {
            return columnFilters.value;
        },
        get columnVisibility() {
            return columnVisibility.value;
        },
        get rowSelection() {
            return rowSelection.value;
        },
        get pagination() {
            return pagination.value;
        },
        get columnPinning() {
            return columnPinning.value;
        },
    },
    enableRowSelection: true,
    enableColumnPinning: true,
    manualPagination: true,
    manualFiltering: true,
    manualSorting: true,
    pageCount: props.data.last_page,
    onPaginationChange: (updater) => {
        if (typeof updater === 'function') {
            pagination.value = updater(pagination.value);
        } else {
            pagination.value = updater;
        }
        makeRequest(getBaseParams());
    },
    onSortingChange: (updaterOrValue) => {
        if (typeof updaterOrValue === 'function') {
            sorting.value = updaterOrValue(sorting.value);
        } else {
            sorting.value = updaterOrValue;
        }
        makeRequest(getBaseParams());
    },
    onColumnFiltersChange: (updaterOrValue) => {
        if (typeof updaterOrValue === 'function') {
            columnFilters.value = updaterOrValue(columnFilters.value);
        } else {
            columnFilters.value = updaterOrValue;
        }
        makeRequest(getBaseParams());
    },
    onColumnVisibilityChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnVisibility),
    onRowSelectionChange: (updaterOrValue) => valueUpdater(updaterOrValue, rowSelection),
    onColumnPinningChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnPinning),
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedUniqueValues: getFacetedUniqueValues(),
});

const setColumnVisibility = (table: TTable<any>, columnsToHide: string[]) => {
    columnsToHide.forEach((columnId) => {
        const column = table.getColumn(columnId);
        if (column) {
            column.toggleVisibility(false);
        }
    });
};

// Set Default Hide Columns
setColumnVisibility(table, props.tableInfo?.hidden_columns);

// Functions to handle column pinning
const pinColumnLeft = (columnId: string) => {
    const currentPinning = columnPinning.value;
    const leftPinned = currentPinning.left || [];
    const rightPinned = currentPinning.right || [];

    // Remove from right if it's there
    const newRightPinned = rightPinned.filter((id) => id !== columnId);

    // Add to left if not already there
    const newLeftPinned = leftPinned.includes(columnId) ? leftPinned : [...leftPinned, columnId];

    columnPinning.value = {
        left: newLeftPinned,
        right: newRightPinned,
    };
};

const pinColumnRight = (columnId: string) => {
    const currentPinning = columnPinning.value;
    const leftPinned = currentPinning.left || [];
    const rightPinned = currentPinning.right || [];

    // Remove from left if it's there
    const newLeftPinned = leftPinned.filter((id) => id !== columnId);

    // Add to right if not already there
    const newRightPinned = rightPinned.includes(columnId) ? rightPinned : [...rightPinned, columnId];

    columnPinning.value = {
        left: newLeftPinned,
        right: newRightPinned,
    };
};

const unpinColumn = (columnId: string) => {
    const currentPinning = columnPinning.value;
    columnPinning.value = {
        left: (currentPinning.left || []).filter((id) => id !== columnId),
        right: (currentPinning.right || []).filter((id) => id !== columnId),
    };
};

// Expose functions for toolbar
defineExpose({
    pinColumnLeft,
    pinColumnRight,
    unpinColumn,
    table,
});
</script>

<template>
    <div class="space-y-4">
        <DataTableToolbar
            :data="data"
            :table="table"
            :search="props.data.search"
            :tableInfo="props.tableInfo"
            :onActionClick="(action: ToolbarAction, data: any) => action.onClick?.(data)"
            :pinColumnLeft="pinColumnLeft"
            :pinColumnRight="pinColumnRight"
            :unpinColumn="unpinColumn"
        />
        <div class="overflow-x-auto rounded-md border">
            <Table class="relative">
                <TableHeader>
                    <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead
                            v-for="header in headerGroup.headers"
                            :key="header.id"
                            :class="{
                                'sticky left-0 z-10 border-r bg-background shadow-sm': header.column.getIsPinned() === 'left',
                                'sticky right-0 z-10 border-l bg-background shadow-sm': header.column.getIsPinned() === 'right',
                            }"
                            :style="{
                                left: header.column.getIsPinned() === 'left' ? `${header.column.getStart('left')}px` : undefined,
                                right: header.column.getIsPinned() === 'right' ? `${header.column.getAfter('right')}px` : undefined,
                            }"
                        >
                            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows.length">
                        <TableRow v-for="row in table.getRowModel().rows" :key="row.id" :data-state="row.getIsSelected() && 'selected'">
                            <TableCell
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                                :class="{
                                    'sticky left-0 z-10 border-r bg-background': cell.column.getIsPinned() === 'left',
                                    'sticky right-0 z-10 border-l bg-background': cell.column.getIsPinned() === 'right',
                                }"
                                :style="{
                                    left: cell.column.getIsPinned() === 'left' ? `${cell.column.getStart('left')}px` : undefined,
                                    right: cell.column.getIsPinned() === 'right' ? `${cell.column.getAfter('right')}px` : undefined,
                                }"
                            >
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell :colspan="props.columns.length" class="h-24 text-center">
                                {{ trans('composable.data_table.no_data') }}
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>
        <DataTablePagination :table="table" v-if="!tableInfo.is_hide_pagination" />
    </div>
</template>
