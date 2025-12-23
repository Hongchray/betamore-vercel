<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Combobox from '@/composables/ui/combobox/Combobox.vue';
import DateRangePicker from '@/composables/ui/date-picker/DateRangePicker.vue';
import { ActionType } from '@/enums/action_menu';
import { PageData, TableInfo, ToolbarAction } from '@/interfaces/ITable';
import { router } from '@inertiajs/vue3';
import { CrossCircledIcon } from '@radix-icons/vue';
import type { ColumnFiltersState, PaginationState, SortingState, Table } from '@tanstack/vue-table';
import { RefreshCw, Search } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import DataTableFacetedFilter from './DataTableFacetedFilter.vue';
import DataTableViewOptions from './DataTableViewOptions.vue';

interface DataTableToolbarProps<T> {
    table: Table<T>;
    data: PageData<T>;
    search: string;
    tableInfo: TableInfo;
    onActionClick: Function;
}

const props = defineProps<DataTableToolbarProps<any>>();
const pagination = ref<PaginationState>({
    pageIndex: props.data.current_page - 1,
    pageSize: props.data.per_page,
});
const filerCols = ref<ColumnFiltersState[]>(props.data.filter);

if (props.data.filter_by) {
    filerCols.value.push([
        {
            id: 'filter_by',
            value: props.data.filter_by,
        },
    ]);
}
const userSelectedDate = ref(false);

const columnFilters = ref<ColumnFiltersState[]>(filerCols.value);
const sorting = ref<SortingState>([]);
const searchValue = ref(props.search);
const filter_toolbar = ref(props.tableInfo.filter_toolbar);

// Selected filter
const selectedFilter = ref(props.data.filter_by);
const filterSearchValue = ref(props.data.filter_value);

const dateRange = ref<{ start_date: Date | null; end_date: Date | null }>({
    start_date: null,
    end_date: null,
});

if (props.data.start_date && props.data.end_date) {
    dateRange.value = {
        start_date: new Date(props.data.start_date),
        end_date: new Date(props.data.end_date),
    };
} else {
    const now = new Date();
    const startDate = new Date();
    startDate.setDate(now.getDate() - 30);
    startDate.setHours(0, 0, 0, 0); // Start of day

    const endDate = new Date();
    endDate.setHours(23, 59, 59, 999); // End of day

    dateRange.value = {
        start_date: startDate,
        end_date: endDate,
    };
}

const hasDateFilter = computed(() => {
    return !!dateRange.value.start_date && !!dateRange.value.end_date;
});

const getBaseParams = () => {
    let filters = {};
    if (columnFilters.value) {
        filters = columnFilters.value.reduce((acc: Record<string, unknown>, filter: any) => {
            if (Array.isArray(filter)) {
                filter.forEach((f) => {
                    if (f.id && f.value !== undefined) {
                        acc[f.id] = f.value;
                    }
                });
            } else if (filter.id && filter.value !== undefined) {
                acc[filter.id] = filter.value;
            }
            return acc;
        }, {});
    }

    const hasDateRangeFilter = !!props.tableInfo?.date_range_filter;

    return {
        page: 1,
        per_page: pagination.value.pageSize,
        table_id: props.tableInfo?.table_id,
        sort_field: sorting.value[0]?.id,
        sort_direction: sorting.value.length === 0 ? undefined : sorting.value[0]?.desc ? 'desc' : 'asc',
        search: searchValue.value,
        ...(props.data.filter_type ? { filter_type: props.data.filter_type } : {}),
        ...(props.data.filter_order_status ? { filter_order_status: props.data.filter_order_status } : {}),
        ...filters,
        ...(selectedFilter.value && filterSearchValue.value
            ? {
                  filter_by: selectedFilter.value,
                  filter_value: filterSearchValue.value,
              }
            : {}),
        ...(hasDateRangeFilter
            ? {
                  start_date: dateRange.value.start_date?.toISOString(),
                  end_date: dateRange.value.end_date?.toISOString(),
              }
            : {}),
    };
};

const makeRequest = (params: any) => {
    router.get(
        typeof props.tableInfo.route === 'string' ? route(props.tableInfo.route) : route(props.tableInfo.route?.name, props.tableInfo.route?.params),
        params,
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const handleSearch = (event: KeyboardEvent) => {
    if (event.key === 'Enter') {
        makeRequest(getBaseParams());
    }
};

watch(searchValue, () => {
    if (searchValue.value === null || searchValue.value === '') {
        makeRequest(getBaseParams());
    }
});

const handleAction = (action: ToolbarAction) => {
    if (action.type == ActionType.Link) {
        router.get(typeof action.value === 'string' ? route(action.value) : route(action.value?.name, action.value?.params));
    }
    if (action.onClick) {
        props.onActionClick?.(action);
    }
};

const applyFilter = () => {
    if (selectedFilter.value && filterSearchValue.value) {
        makeRequest(getBaseParams());
    }
};

const handleFilterSearch = (event: KeyboardEvent) => {
    if (event.key === 'Enter') {
        applyFilter();
    }
};

const isFiltered = computed(() => props.table.getState().columnFilters.length > 0);

const resetFilters = () => {
    props.table.resetColumnFilters();
    columnFilters.value = [];
    selectedFilter.value = '';
    filterSearchValue.value = '';
    dateRange.value = { start_date: null, end_date: null };
    makeRequest(getBaseParams());
};

const handleDateRangeChange = (value: { start: any; end: any }) => {
    userSelectedDate.value = true;

    dateRange.value = {
        start_date: value.start ? new Date(value.start) : null,
        end_date: value.end ? new Date(value.end) : null,
    };

    if (hasDateFilter.value) {
        makeRequest(getBaseParams());
    }
};

const handleRefresh = () => {
    dateRange.value = { start_date: null, end_date: null };

    const params = getBaseParams();
    delete params.start_date;
    delete params.end_date;

    router.get(route(route().current() as string), params, {
        preserveScroll: true,
        preserveState: false,
    });
};

onMounted(() => {
    if (!props.data.start_date || !props.data.end_date) {
        makeRequest(getBaseParams());
    }
});
</script>

<template>
    <div class="flex w-full items-end justify-between">
        <!-- Left side: search, filters -->
        <div class="flex flex-1 flex-wrap items-end gap-2 space-x-2">
            <div class="relative" v-if="tableInfo.is_searchable ?? true">
                <Search class="absolute top-2 left-2.5 h-4 w-4 text-muted-foreground" />
                <Input
                    type="search"
                    autocomplete="off"
                    v-model="searchValue"
                    @keydown="handleSearch"
                    placeholder="Search.."
                    class="h-8 w-full min-w-[250px] appearance-none bg-background pl-8 shadow-none"
                />
            </div>

            <!-- filters loop -->
            <div v-for="filter in filter_toolbar" :key="filter.title" class="grid gap-2">
                <DataTableFacetedFilter
                    :table="table"
                    :column="table.getColumn(filter.column)"
                    :title="filter.title"
                    :options="filter.data"
                    class="min-w-[200px]"
                />
            </div>

            <!-- filter by combo -->
            <div class="grid gap-2" v-if="tableInfo.filter_by_option">
                <Label>{{ tableInfo.filter_by_option.title }}</Label>
                <div class="flex items-center space-x-2">
                    <Combobox
                        :items="tableInfo.filter_by_option.data"
                        buttonClass="h-8 w-52"
                        v-model="selectedFilter"
                        placeholder="Filter by..."
                        defaultLabel="All"
                    />
                    <div class="relative" v-if="selectedFilter">
                        <Search class="absolute top-2 left-2.5 h-4 w-4 text-muted-foreground" />
                        <Input
                            type="search"
                            autocomplete="off"
                            v-model="filterSearchValue"
                            @keydown="handleFilterSearch"
                            placeholder="Search in filter..."
                            class="h-8 w-full min-w-[200px] appearance-none bg-background pl-8 shadow-none"
                        />
                    </div>
                </div>
            </div>

            <!-- reset button -->
            <Button v-if="isFiltered" variant="ghost" class="h-8 px-2 lg:px-3" @click="resetFilters">
                Reset
                <CrossCircledIcon class="ml-2 h-4 w-4" />
            </Button>
        </div>

        <!-- Right side: date filter + buttons -->
        <div class="flex items-end gap-2">
            <div v-if="tableInfo.date_range_filter" class="flex items-center gap-2">
                <DateRangePicker size="sm" @update:dateRange="handleDateRangeChange" :model-value="dateRange" />
                <Button v-if="hasDateFilter && userSelectedDate" variant="outline" size="sm" @click="handleRefresh">
                    <RefreshCw class="mx-3 h-4 w-4" />
                </Button>
            </div>

            <div class="px-2">
                <div class="flex items-center space-x-2">
                    <Button size="sm" class="ml-auto h-8" v-for="buton in tableInfo.button_toolbar" :key="buton.label" @click="handleAction(buton)">
                        <component class="mr-2 h-4 w-4" :is="buton.icon" v-if="buton.icon" />
                        {{ buton.label }}
                    </Button>
                </div>
            </div>
        </div>

        <DataTableViewOptions :table="table" />
    </div>
</template>
