<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import DateRangePicker from '@/composables/ui/date-picker/DateRangePicker.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { User } from '@/pages/User/data/schema';
import { type BreadcrumbItem } from '@/types';
import { formatCurrency } from '@/utils/formatCurrency';
import { Head, router, usePage } from '@inertiajs/vue3';
import { CalendarDate } from '@internationalized/date';
import { trans } from 'laravel-vue-i18n';
import { AlertCircle, Calendar, CheckCircle, Clock, DollarSign, RefreshCw, ShoppingCart, TrendingUp, Users } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface DashboardData {
    monthly_data: any;
    total_orders: number;
    total_customer: number;
    revenue: {
        total_revenue: string;
        paid_revenue: number;
        pending_revenue: string;
        completed_revenue: number;
        monthly_revenue: number;
        daily_revenue: number;
        last_30_days_revenue: number;
        revenue_by_payment_status: {
            pending: string;
        };
    };
    top_users: Array<{
        id: string;
        first_name: string;
        last_name: string;
        email: string;
        phone: string;
        image: string;
        total_orders: number;
        total_spent: number;
        last_order_date: string;
        formatted_total_spent: string;
        last_order_human: string;
        monthly_data: string;
    }>;
}

const props = defineProps<{
    data: DashboardData;
}>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: trans('dashboard.dashboard'),
        href: '/dashboard',
    },
]);

// Format currency

// Calculate percentage for pending revenue
const pendingPercentage = () => {
    const total = parseFloat(props.data.revenue.total_revenue);
    const pending = parseFloat(props.data.revenue.pending_revenue);
    return total > 0 ? Math.round((pending / total) * 100) : 0;
};

const selectedDateRange = ref<{ start: CalendarDate | null; end: CalendarDate | null }>({
    start: null,
    end: null,
});
const handleDateRangeChange = (dateRange: { start: CalendarDate; end: CalendarDate }) => {
    selectedDateRange.value = dateRange;

    const startDate = `${dateRange.start.year}-${String(dateRange.start.month).padStart(2, '0')}-${String(dateRange.start.day).padStart(2, '0')}`;
    const endDate = `${dateRange.end.year}-${String(dateRange.end.month).padStart(2, '0')}-${String(dateRange.end.day).padStart(2, '0')}`;

    // Reload dashboard with date filter
    router.get(
        route('dashboard'),
        {
            start_date: startDate,
            end_date: endDate,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const handleRefresh = () => {
    router.visit(route().current());
};

const hasDateFilter = computed(() => {
    const page = usePage();
    return page.url.includes('start_date') || page.url.includes('end_date');
});
const translatedMonths = computed(() => [
    trans('dashboard.months.jan'),
    trans('dashboard.months.feb'),
    trans('dashboard.months.mar'),
    trans('dashboard.months.apr'),
    trans('dashboard.months.may'),
    trans('dashboard.months.jun'),
    trans('dashboard.months.jul'),
    trans('dashboard.months.aug'),
    trans('dashboard.months.sep'),
    trans('dashboard.months.oct'),
    trans('dashboard.months.nov'),
    trans('dashboard.months.dec'),
]);

const series = computed(() => [
    {
        name: trans('dashboard.customer_registrations'),
        data: props.data.monthly_data?.customers || [],
    },
    {
        name: trans('dashboard.orders'),
        data: props.data.monthly_data?.orders || [],
    },
]);

const chartOptions = computed(() => ({
    chart: {
        height: 350,
        type: 'line',
        fontFamily: 'Kantumruy Pro, sans-serif',
        background: 'var(--chart-bg)',
        dropShadow: {
            enabled: true,
            color: 'rgba(0,0,0,0.4)',
            top: 18,
            left: 7,
            blur: 12,
            opacity: 0.25,
        },
        zoom: {
            enabled: false,
        },
        toolbar: {
            show: false,
        },
    },

    // Line colors (Light/Dark friendly)
    colors: ['#3B82F6', '#10B981'],

    dataLabels: {
        enabled: true,
        style: {
            fontFamily: 'Kantumruy Pro, sans-serif',
            colors: ['var(--chart-text)'],
        },
    },

    stroke: {
        curve: 'smooth',
        width: 3,
    },

    title: {
        text: `${trans('dashboard.customer_orders_chart_title')} - ${props.data.monthly_data?.year || new Date().getFullYear()}`,
        align: 'left',
        style: {
            fontSize: '16px',
            fontWeight: 600,
            fontFamily: 'Kantumruy Pro, sans-serif',
            color: 'var(--chart-text)',
        },
    },

    grid: {
        borderColor: 'var(--chart-grid)',
        row: {
            colors: ['rgba(249,250,251,0.5)', 'transparent'],
            opacity: 0.5,
        },
    },

    markers: {
        size: 4,
        strokeColors: 'var(--chart-text)',
    },

    xaxis: {
        categories: translatedMonths.value,
        labels: {
            style: {
                fontFamily: 'Kantumruy Pro, sans-serif',
                colors: 'var(--chart-text)',
            },
        },
        title: {
            text: trans('dashboard.month'),
            style: {
                fontFamily: 'Kantumruy Pro, sans-serif',
                color: 'var(--chart-text)',
            },
        },
        axisBorder: {
            color: 'var(--chart-grid)',
        },
        axisTicks: {
            color: 'var(--chart-grid)',
        },
    },

    yaxis: {
        min: 0,
        labels: {
            style: {
                fontFamily: 'Kantumruy Pro, sans-serif',
                colors: 'var(--chart-text)',
            },
        },
        title: {
            text: trans('dashboard.count'),
            style: {
                fontFamily: 'Kantumruy Pro, sans-serif',
                color: 'var(--chart-text)',
            },
        },
    },

    legend: {
        position: 'top',
        horizontalAlign: 'right',
        floating: true,
        offsetY: -25,
        offsetX: -5,
        fontFamily: 'Kantumruy Pro, sans-serif',
        labels: {
            colors: 'var(--chart-text)',
        },
    },

    tooltip: {
        shared: true,
        intersect: false,
        theme: 'dark', // Auto-detects dark mode
        style: {
            fontFamily: 'Kantumruy Pro, sans-serif',
        },
    },
}));

const page = usePage();
const user = page.props.auth.user as unknown as User;
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ trans('dashboard.dashboard') }}
                    </h1>
                    <p class="text-muted-foreground">
                        {{ trans('dashboard.welcome_message', { name: user.first_name + ' ' + user.last_name }) }}
                    </p>
                </div>

                <div class="flex flex-col gap-4">
                    <div class="flex gap-2">
                        <DateRangePicker @update:dateRange="handleDateRangeChange" />
                        <Button v-if="hasDateFilter" variant="outline" size="lg" @click="handleRefresh">
                            <RefreshCw className="mr-2 h-4 w-4" />
                            Refresh
                        </Button>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-5">
                <div
                    class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ trans('dashboard.total_orders') }}</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ data.total_orders }}</p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/20">
                            <ShoppingCart class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <TrendingUp class="mr-1 h-4 w-4 text-green-500" />
                        <span class="text-green-600 dark:text-green-400">{{ trans('dashboard.active_orders') }}</span>
                    </div>
                </div>

                <div
                    class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ trans('dashboard.total_customers') }}</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ data.total_customer }}</p>
                        </div>
                        <div class="rounded-full bg-green-100 p-3 dark:bg-green-900/20">
                            <Users class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <CheckCircle class="mr-1 h-4 w-4 text-green-500" />
                        <span class="text-green-600 dark:text-green-400">{{ trans('dashboard.registered_users') }}</span>
                    </div>
                </div>

                <div
                    class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ trans('dashboard.total_revenue') }}</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(data.revenue.total_revenue) }}</p>
                        </div>
                        <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900/20">
                            <DollarSign class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <TrendingUp class="mr-1 h-4 w-4 text-purple-500" />
                        <span class="text-purple-600 dark:text-purple-400">{{ trans('dashboard.all_time_earnings') }}</span>
                    </div>
                </div>

                <div
                    class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ trans('dashboard.pending_revenue') }}</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(data.revenue.pending_revenue) }}</p>
                        </div>
                        <div class="rounded-full bg-orange-100 p-3 dark:bg-orange-900/20">
                            <Clock class="h-6 w-6 text-orange-600 dark:text-orange-400" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <AlertCircle class="mr-1 h-4 w-4 text-orange-500" />
                        <span class="text-orange-600 dark:text-orange-400">{{ pendingPercentage() }}% {{ trans('dashboard.of_total') }}</span>
                    </div>
                </div>

                <div
                    class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ trans('dashboard.paid') }}</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(data.revenue.paid_revenue) }}</p>
                        </div>
                        <div class="rounded-full bg-green-100 p-3 dark:bg-green-900/20">
                            <Users class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <CheckCircle class="mr-1 h-4 w-4 text-green-500" />
                        <span class="text-green-600 dark:text-green-400">{{ trans('dashboard.total_paid') }}</span>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-900">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ trans('dashboard.revenue_overview') }}</h3>
                            <Calendar class="h-5 w-5 text-gray-500" />
                        </div>

                        <div class="space-y-6">
                            <div>
                                <div class="mb-2 flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ trans('dashboard.revenue_status') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(data.revenue.total_revenue) }}</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                    <div
                                        class="h-2 rounded-full bg-gradient-to-r from-orange-400 to-orange-600"
                                        :style="{ width: pendingPercentage() + '%' }"
                                    ></div>
                                </div>
                                <div class="mt-2 flex justify-between text-xs text-gray-500">
                                    <span>{{ trans('dashboard.pending') }}: {{ formatCurrency(data.revenue.pending_revenue) }}</span>
                                    <span>{{ pendingPercentage() }}% {{ trans('dashboard.pending') }}</span>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ trans('dashboard.paid_revenue') }}</p>
                                            <p class="text-xl font-bold text-green-600 dark:text-green-400">
                                                {{ formatCurrency(data.revenue.paid_revenue) }}
                                            </p>
                                        </div>
                                        <CheckCircle class="h-8 w-8 text-green-500" />
                                    </div>
                                </div>

                                <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ trans('dashboard.completed_revenue') }}</p>
                                            <p class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                                {{ formatCurrency(data.revenue.completed_revenue) }}
                                            </p>
                                        </div>
                                        <TrendingUp class="h-8 w-8 text-blue-500" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-900">
                    <h3 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white">{{ trans('dashboard.quick_stats') }}</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 pb-3 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ trans('dashboard.monthly_revenue') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(data.revenue.monthly_revenue) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-200 pb-3 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ trans('dashboard.daily_revenue') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(data.revenue.daily_revenue) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-200 pb-3 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ trans('dashboard.last_30_days') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(data.revenue.last_30_days_revenue) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <Card class="lg:col-span-2">
                    <div id="chart" class="">
                        <apexchart type="line" height="350" :options="chartOptions" :series="series"></apexchart>
                    </div>
                </Card>
                <Card class="lg:col-span-1">
                    <CardContent class="p-6">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ trans('dashboard.top_customers') }}</h3>
                            <Users class="h-5 w-5 text-gray-500" />
                        </div>

                        <div class="space-y-4">
                            <div
                                v-for="(user, index) in data.top_users"
                                :key="user.id"
                                class="flex items-center justify-between border-b border-gray-200 pb-3 last:border-b-0 last:pb-0 dark:border-gray-700"
                            >
                                <div class="flex items-center space-x-3">
                                    <!-- Ranking Badge -->
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400': index === 0,
                                            'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300': index === 1,
                                            'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400': index === 2,
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400': index > 2,
                                        }"
                                    >
                                        {{ index + 1 }}
                                    </div>

                                    <!-- User Avatar -->
                                    <div class="relative">
                                        <img
                                            v-if="user.image"
                                            :src="user.image"
                                            :alt="`${user.first_name} ${user.last_name}`"
                                            class="h-10 w-10 rounded-full object-cover"
                                        />
                                        <div v-else class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-300 dark:bg-gray-600">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ user.first_name?.charAt(0) }}{{ user.last_name?.charAt(0) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- User Info -->
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                            {{ user.first_name }} {{ user.last_name }}
                                        </p>
                                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                            {{ user.phone }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ user.last_order_human }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Stats -->
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ user.formatted_total_spent }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ user.total_orders }} {{ user.total_orders === 1 ? 'order' : 'orders' }}
                                    </p>
                                </div>
                            </div>

                            <div v-if="!data.top_users || data.top_users.length === 0" class="py-8 text-center">
                                <Users class="mx-auto h-12 w-12 text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ trans('dashboard.no_customers') }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ trans('dashboard.no_customers_hint') }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
