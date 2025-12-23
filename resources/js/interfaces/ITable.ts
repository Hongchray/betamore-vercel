import { ActionType } from '@/enums/action_menu';
import type { Component } from 'vue';
import { RouteName, RouteParams } from 'ziggy-js';
interface PageData<T> {
    [x: string]: any;
    id: unknown;
    data: T[];
    current_page: number;
    per_page: number;
    last_page: number;
    total: number;
    filter?: any;
    filter_type?: string; // filter type Today, Yesterday, This Week, This Month, This Year
    filter_order_status?: string; // filter status of order
    search: string;
    sort_field: string;
    sort_direction: string;

    filter_by?: string; // filter by column name
    filter_value?: string; // filter value
}
interface TableInfo {
    table_id?: string;
    is_searchable?: boolean;
    route: RouteConfig | string;
    is_hide_manage_column?: boolean;
    is_hide_pagination?: boolean;
    hidden_columns: string[];
    button_toolbar: ToolbarAction[];
    filter_toolbar?: filterToolbar[];
    filter_by_option?: filterByOption;
    pinned_left?: string[]; // Add this
    pinned_right?: string[]; // Add this
    date_range_filter?: {
        start_date: string;
        end_date?: string;
    };
}

interface ToolbarAction {
    value: RouteConfig | string;
    type: ActionType;
    isHide?: boolean;
    label: string;
    class?: string;
    icon?: Component;
    onClick?: (data?: any) => void;
}
interface filterToolbar {
    title: string;
    column: string;
    data: filterToolbarData[];
}
interface filterByOption {
    title: string;
    data: filterToolbarData[];
}
interface filterToolbarData {
    value: string;
    label: string;
    icon?: Component;
}

interface RouteConfig<T extends RouteName = RouteName> {
    name: T;
    params?: RouteParams<T>;
}
export type { PageData, RouteConfig, TableInfo, ToolbarAction };
