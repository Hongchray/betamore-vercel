<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { MixerHorizontalIcon } from '@radix-icons/vue';
import type { Table } from '@tanstack/vue-table';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';

interface DataTableViewOptionsProps<T> {
    table: Table<T>;
}
const props = defineProps<DataTableViewOptionsProps<any>>();

const columns = computed(() => {
    const allColumns = props.table.getAllColumns();
    const filteredColumns = allColumns.filter((column) => {
        return column.getCanHide() && column.id !== 'select' && column.id !== 'actions' && typeof column.accessorFn !== 'undefined';
    });

    return filteredColumns;
});

// Add this function to handle toggle
const handleToggle = (column: any, value: boolean) => {
    console.log('Before toggle:', column.id, 'visible:', column.getIsVisible());
    column.toggleVisibility(value);
    console.log('After toggle:', column.id, 'visible:', column.getIsVisible());

    // Force table re-render if needed
    props.table.options.onStateChange?.();
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="outline" size="sm" class="ml-auto flex h-8">
                <MixerHorizontalIcon class="mr-2 h-4 w-4" />
                {{ trans('composable.data_table_view_options.toggle_columns') }}
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="w-[200px]">
            <DropdownMenuLabel>{{ trans('composable.data_table_view_options.toggle_columns') }}</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <div
                v-for="column in columns"
                :key="column.id"
                class="flex cursor-pointer items-center space-x-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent"
                @click="column.toggleVisibility()"
            >
                <input type="checkbox" :checked="column.getIsVisible()" class="h-4 w-4" @click.stop />
                <span class="capitalize">{{ column.id.replace('_', ' ') }}</span>
            </div>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
