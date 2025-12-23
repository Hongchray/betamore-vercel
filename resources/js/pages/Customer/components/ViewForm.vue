<template>
    <div class="">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <Card class="overflow-hidden rounded-2xl border-0 shadow-lg ring-1 ring-gray-200/50 dark:bg-gray-900 dark:ring-white/10">
                    <CardContent class="p-0">
                        <!-- Header -->
                        <div class="border-2 border-t-8 border-b-8 border-gray-100 px-6 py-8 dark:border-gray-700">
                            <div class="relative text-center" role="region" aria-label="User Profile">
                                <!-- Avatar -->
                                <div class="relative mx-auto mb-4 inline-block">
                                    <Avatar class="h-24 w-24 ring-4 ring-white/30">
                                        <AvatarImage :src="user?.image ?? ''" :alt="`${user?.first_name ?? 'User'}'s avatar`" />
                                        <AvatarFallback>
                                            {{ (user?.first_name?.[0] ?? '') + (user?.last_name?.[0] ?? '') || 'NA' }}
                                        </AvatarFallback>
                                    </Avatar>
                                </div>

                                <h2 v-if="user?.first_name || user?.last_name" class="text-xl font-bold">
                                    {{ user?.first_name }} {{ user?.last_name }}
                                </h2>

                                <p v-if="user?.email" class="mt-1 truncate" title="User Email">
                                    {{ user.email }}
                                </p>

                                <div v-if="user?.gender" class="mt-3">
                                    <span
                                        class="inline-flex items-center rounded-full bg-white/20 px-3 py-1 text-xs font-medium text-white capitalize backdrop-blur-sm"
                                        aria-label="User gender"
                                    >
                                        {{ user.gender || 'Not specified' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-2 p-6">
                            <Link :href="route('customers.edit', user?.id)">
                                <Button
                                    variant="outline"
                                    class="w-full justify-center border-gray-200 text-gray-700 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 dark:border-gray-700 dark:text-gray-200 dark:hover:border-blue-500 dark:hover:bg-gray-800"
                                >
                                    <Edit class="mr-2 h-4 w-4" />
                                    {{ trans('customer.view.edit_customer') }}
                                </Button>
                            </Link>

                            <!-- <Button>
                                <Mail class="mr-2 h-4 w-4" />
                                {{ trans('customer.view.send_message') }}
                            </Button> -->
                        </div>

                        <!-- Contact Info -->
                        <div class="border-t border-gray-100 px-6 py-5 dark:border-gray-700">
                            <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-900 uppercase dark:text-white">
                                {{ trans('customer.view.contact_information') }}
                            </h3>
                            <div class="space-y-4">
                                <div class="group flex items-center">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50 group-hover:bg-blue-50 dark:bg-gray-800 dark:group-hover:bg-blue-900"
                                    >
                                        <Phone
                                            class="h-4 w-4 text-gray-500 group-hover:text-blue-600 dark:text-gray-400 dark:group-hover:text-blue-400"
                                        />
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ user?.phone }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ trans('customer.view.mobile') }}</p>
                                    </div>
                                </div>

                                <div class="group flex items-center">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50 group-hover:bg-green-50 dark:bg-gray-800 dark:group-hover:bg-green-900"
                                    >
                                        <Calendar
                                            class="h-4 w-4 text-gray-500 group-hover:text-green-600 dark:text-gray-400 dark:group-hover:text-green-400"
                                        />
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatDateTable(user?.created_at ?? '') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ trans('customer.view.member_since') }}</p>
                                    </div>
                                </div>

                                <div class="group flex items-center">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50 group-hover:bg-purple-50 dark:bg-gray-800 dark:group-hover:bg-purple-900"
                                    >
                                        <CreditCard
                                            class="h-4 w-4 text-gray-500 group-hover:text-purple-600 dark:text-gray-400 dark:group-hover:text-purple-400"
                                        />
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ order_count }} {{ trans('customer.view.order') }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ trans('customer.view.total_orders') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-5 dark:border-gray-700 dark:bg-gray-800/50">
                            <div class="text-center">
                                <div class="mb-2">
                                    <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(total_paid) }}</span>
                                </div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ trans('customer.view.total_spent') }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="lg:col-span-2">
                <Tabs
                    v-model="activeTab"
                    class="overflow-hidden rounded-2xl border-0 bg-white shadow-xl ring-1 ring-gray-200/50 dark:bg-gray-900 dark:ring-gray-800/50"
                >
                    <TabsList
                        class="grid min-h-12 w-full grid-cols-3 border-b border-gray-200/60 bg-gray-50/50 p-1 dark:border-gray-800/60 dark:bg-gray-800/50"
                    >
                        <TabsTrigger
                            v-for="tab in tabs"
                            :key="tab.id"
                            :value="tab.id"
                            class="relative flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 data-[state=active]:bg-white data-[state=active]:text-blue-600 data-[state=active]:shadow-sm data-[state=inactive]:text-gray-600 data-[state=inactive]:hover:text-gray-900 dark:data-[state=active]:bg-gray-700 dark:data-[state=active]:text-blue-400 dark:data-[state=inactive]:text-gray-400 dark:data-[state=inactive]:hover:text-gray-200"
                        >
                            <component :is="getTabIcon(tab.id)" class="mr-2 h-4 w-4" />
                            {{ trans(`customer.view.tabs.${tab.id}`) }}

                            <span
                                v-if="activeTab === tab.id"
                                class="absolute bottom-0 left-1/2 h-0.5 w-8 -translate-x-1/2 rounded-full bg-blue-600 dark:bg-blue-400"
                            ></span>
                        </TabsTrigger>
                    </TabsList>

                    <!-- OVERVIEW TAB -->
                    <TabsContent value="overview" class="p-0">
                        <div class="p-6">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ trans('customer.view.overview.title') }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ trans('customer.view.overview.description') }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                <!-- Contact Information Card -->
                                <div
                                    class="group rounded-xl border border-gray-200/60 bg-gradient-to-br from-blue-50/50 to-indigo-50/30 p-5 transition-all hover:shadow-md dark:border-gray-700/60 dark:from-blue-900/20 dark:to-indigo-900/10 dark:hover:border-gray-600"
                                >
                                    <div class="mb-4 flex items-center">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/50">
                                            <Phone class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <h4 class="ml-3 text-base font-semibold text-gray-900 dark:text-white">
                                            {{ trans('customer.view.overview.contact_info') }}
                                        </h4>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex items-center rounded-lg bg-white/60 p-3 dark:bg-gray-800/60">
                                            <Hash class="mr-3 h-4 w-4 text-gray-500 dark:text-gray-400" />
                                            <div>
                                                <p class="text-xs font-medium tracking-wide text-gray-500 uppercase dark:text-gray-400">
                                                    {{ trans('customer.view.overview.customer_id') }}
                                                </p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ user?.user_id }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center rounded-lg bg-white/60 p-3 dark:bg-gray-800/60">
                                            <Mail class="mr-3 h-4 w-4 text-gray-500 dark:text-gray-400" />
                                            <div>
                                                <p class="text-xs font-medium tracking-wide text-gray-500 uppercase dark:text-gray-400">
                                                    {{ trans('customer.view.overview.email') }}
                                                </p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ user?.email }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center rounded-lg bg-white/60 p-3 dark:bg-gray-800/60">
                                            <Phone class="mr-3 h-4 w-4 text-gray-500 dark:text-gray-400" />
                                            <div>
                                                <p class="text-xs font-medium tracking-wide text-gray-500 uppercase dark:text-gray-400">
                                                    {{ trans('customer.view.overview.phone') }}
                                                </p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ user?.phone }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center rounded-lg bg-white/60 p-3 dark:bg-gray-800/60">
                                            <Calendar class="mr-3 h-4 w-4 text-gray-500 dark:text-gray-400" />
                                            <div>
                                                <p class="text-xs font-medium tracking-wide text-gray-500 uppercase dark:text-gray-400">
                                                    {{ trans('customer.view.overview.join_date') }}
                                                </p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ formatDateTable(user?.created_at ?? '') || trans('customer.view.overview.na') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TabsContent>

                    <!-- ORDERS TAB -->
                    <TabsContent value="orders" class="p-0">
                        <div class="p-6">
                            <div class="mb-6 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ trans('customer.view.orders.title') }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ trans('customer.view.orders.description') }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <Card
                                    v-for="order in user.orders"
                                    :key="order.id"
                                    class="group border-0 bg-white shadow-sm ring-1 ring-gray-200/50 transition-all hover:shadow-md hover:ring-gray-300/50 dark:bg-gray-800 dark:ring-gray-700/50 dark:hover:ring-gray-600/50"
                                >
                                    <CardContent class="py-2">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-start space-x-4">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/50">
                                                    <ShoppingBag class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                                                </div>
                                                <div>
                                                    <div class="flex items-center space-x-1">
                                                        <p class="font-semibold text-gray-900 dark:text-white">
                                                            {{ trans('customer.view.orders.order_number') }} #{{ order.order_number }}
                                                        </p>
                                                        <OrderStatusBadge :status="order.status" :show-icon="true" />
                                                    </div>
                                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                        {{ formatDateTable(order.created_at) }}
                                                    </p>
                                                    <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                        <span class="flex items-center">
                                                            <Package class="mr-1 h-3 w-3" />
                                                            {{ order.items_count }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                                    {{ formatCurrency(order.total_amount) }}
                                                </p>
                                                <Link :href="route('orders.show', order.id)">
                                                    <Button type="button" variant="outline" class="mt-2">
                                                        <Eye class="mr-1 h-3 w-3" />
                                                        {{ trans('customer.view.orders.view_details') }}
                                                    </Button>
                                                </Link>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        </div>
                    </TabsContent>

                    <!-- ADDRESSES TAB -->
                    <TabsContent value="addresses" class="p-0">
                        <div class="p-6">
                            <div class="mb-6 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ trans('customer.view.addresses.title') }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ trans('customer.view.addresses.description') }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <Card
                                    v-for="address in user?.addresses"
                                    :key="address.id"
                                    class="group border-0 bg-white shadow-sm ring-1 ring-gray-200/50 transition-all hover:shadow-md hover:ring-gray-300/50 dark:bg-gray-800 dark:ring-gray-700/50 dark:hover:ring-gray-600/50"
                                >
                                    <CardContent class="py-1">
                                        <div class="flex items-start space-x-4">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/50">
                                                <MapPin class="h-6 w-6 text-green-600 dark:text-green-400" />
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900 dark:text-white">
                                                    {{ trans('customer.view.addresses.contact_name') }}: {{ address.contact_name }}
                                                </h4>
                                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ trans('customer.view.addresses.address') }}: {{ address.address }}
                                                </p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ trans('customer.view.addresses.city') }}: {{ address.city }},
                                                    {{ trans('customer.view.addresses.postal_code') }}: {{ address.postal_code }}
                                                </p>

                                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                    <Phone class="mr-1 inline h-3 w-3" />
                                                    {{ address.phone }}
                                                </p>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>

                                <div v-if="!user?.addresses || user.addresses.length === 0" class="py-8 text-center">
                                    <MapPin class="mx-auto h-12 w-12 text-gray-400" />
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ trans('customer.view.addresses.no_addresses.title') }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ trans('customer.view.addresses.no_addresses.description') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </TabsContent>
                </Tabs>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import OrderStatusBadge from '@/composables/ui/badge-status/OrderStatusBadge.vue';
import { formatCurrency } from '@/utils/formatCurrency';
import { formatDateTable } from '@/utils/formatDate';
import { Link } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { Calendar, CreditCard, Edit, Eye, Hash, Mail, MapPin, Package, Phone, ShoppingBag, UserCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import type { User } from '../data/schema';
const props = defineProps<{
    user?: User | null;
    order_count: number;
    total_paid: number;
}>();

const activeTab = ref('overview');

const tabs = [
    { id: 'overview', name: 'Overview' },
    { id: 'orders', name: 'Orders' },
    { id: 'addresses', name: 'Addresses' },
];

const getTabIcon = (tabId: string) => {
    const icons: Record<string, any> = {
        overview: UserCircle,
        orders: ShoppingBag,
        addresses: MapPin,
    };
    return icons[tabId] || UserCircle;
};
</script>
