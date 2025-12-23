<script setup lang="ts">
// 1. Imports
import { Badge } from '@/components/ui/badge';
import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { useToast } from '@/components/ui/toast/use-toast';
import ImagePreview from '@/composables/ui/image-preview/ImagePreview.vue';
import { router, useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';

import { DeliveryStatus } from '@/enums/DeliveryStatus';
import { OrderStatus } from '@/enums/OrderStatus';
import { PaymentStatus } from '@/enums/PaymentStatus';

import Combobox from '@/composables/ui/combobox/Combobox.vue';
import { formatCurrency } from '@/utils/formatCurrency';
import { formatDateTable } from '@/utils/formatDate';
import { deliveryStatusColors, orderStatusColors, paymentStatusColors } from '@/utils/statusColors';
import { PhTelegramLogo } from '@phosphor-icons/vue';
import {
    CarFront,
    CheckCircle,
    Clock,
    CreditCard,
    Download,
    Loader,
    Mail,
    MapPinIcon,
    Package,
    PackageCheck,
    Phone,
    RefreshCw,
    ShoppingCart,
    Truck,
    UserIcon,
    XCircle,
} from 'lucide-vue-next';
import { Order } from '../data/schema';
// 2. Props & Types
type FormData = Omit<Order, 'id'>;

interface PaymentStep {
    value: PaymentStatus;
    label: string;
}

const props = defineProps<{
    order?: Order | null;
}>();

// 3. Composables & hooks
const { toast } = useToast();

const form = useForm<{
    status: string;
    payment_status: string;
}>({
    status: props.order?.status ?? OrderStatus.PENDING,
    payment_status: props.order?.order_payment?.payment_status ?? PaymentStatus.Pending,
});

// 4. Constants

const statusOrder = [OrderStatus.PENDING, OrderStatus.CONFIRMED, OrderStatus.PROCESSING, OrderStatus.SHIPPED, OrderStatus.DELIVERED];

const orderSteps = computed(() => {
    const currentStatusIndex = statusOrder.indexOf(form.status as OrderStatus);
    return [
        {
            id: 1,
            status: OrderStatus.PENDING,
            title: trans('order.status.pending'),
            icon: Clock,
            timestamp: props.order?.created_at ? formatDateTable(props.order.created_at) : '',
        },
        {
            id: 2,
            status: OrderStatus.CONFIRMED,
            title: trans('order.status.confirmed'),
            icon: CheckCircle,
            timestamp: currentStatusIndex >= 1 && props.order?.updated_at ? formatDateTable(props.order.updated_at) : '',
        },
        {
            id: 3,
            status: OrderStatus.PROCESSING,
            title: trans('order.status.processing'),
            icon: Loader,
            timestamp: currentStatusIndex >= 2 && props.order?.updated_at ? formatDateTable(props.order.updated_at) : '',
        },
        {
            id: 4,
            status: OrderStatus.SHIPPED,
            title: trans('order.status.shipped'),
            icon: Truck,
            timestamp: currentStatusIndex >= 3 && props.order?.updated_at ? formatDateTable(props.order.updated_at) : '',
        },
        {
            id: 5,
            status: OrderStatus.DELIVERED,
            title: trans('order.status.delivered'),
            icon: PackageCheck,
            timestamp: currentStatusIndex >= 4 && props.order?.updated_at ? formatDateTable(props.order.updated_at) : '',
        },
    ];
});

const paymentSteps: PaymentStep[] = [
    { value: PaymentStatus.Pending, label: 'Pending' },
    { value: PaymentStatus.Approved, label: 'Approved' },
    { value: PaymentStatus.Declined, label: 'Declined' },
    { value: PaymentStatus.Refunded, label: 'Refunded' },
    { value: PaymentStatus.Cancelled, label: 'Cancelled' },
];

// 5. Helper functions

const getStatusColor = (status: string, type: 'order' | 'delivery' | 'payment') => {
    switch (type) {
        case 'order':
            return orderStatusColors[status as OrderStatus] || orderStatusColors.PENDING;
        case 'delivery':
            return deliveryStatusColors[status as DeliveryStatus] || deliveryStatusColors.PENDING;
        case 'payment':
            return paymentStatusColors[status as PaymentStatus] || paymentStatusColors.PENDING;
        default:
            return '';
    }
};

const getPaymentStatusColor = (status: PaymentStatus): string => {
    const colors: Record<PaymentStatus, string> = {
        [PaymentStatus.Pending]: 'border-yellow-500 text-yellow-700 bg-yellow-50',
        [PaymentStatus.Approved]: 'border-green-500 text-green-700 bg-green-50',
        [PaymentStatus.Declined]: 'border-red-500 text-red-700 bg-red-50',
        [PaymentStatus.Refunded]: 'border-blue-500 text-blue-700 bg-blue-50',
        [PaymentStatus.Cancelled]: 'border-gray-500 text-gray-700 bg-gray-50',
    };
    return colors[status] || 'border-gray-300 text-gray-600 bg-gray-50'; // fallback
};

const getStepStyles = (status: OrderStatus, index: number) => {
    const isCancelled = form.status === OrderStatus.CANCELLED;
    const isActive = index === currentStepIndex.value;
    const isCompleted = index < currentStepIndex.value;
    const isUpcoming = index > currentStepIndex.value;

    if (isCancelled) {
        return {
            circle: isActive
                ? 'border-red-600 bg-red-600 text-white dark:border-red-500 dark:bg-red-600'
                : 'border-red-300 text-red-400 dark:border-red-500 dark:text-red-300',
            icon: isActive ? 'text-red-600' : 'text-red-400 dark:text-red-300',
            text: 'text-red-600 dark:text-red-400',
            badge: 'border-red-500 bg-red-50 text-red-600 dark:border-red-400 dark:bg-red-900 dark:text-red-300',
        };
    }

    if (isCompleted) {
        return {
            circle: 'border-blue-600 bg-blue-600 text-white dark:border-blue-500 dark:bg-blue-600',
            icon: 'text-gray-500',
            text: 'text-blue-600 dark:text-blue-300',
            badge: 'border-blue-500 bg-blue-50 text-blue-600 dark:border-blue-400 dark:bg-blue-900 dark:text-blue-300',
        };
    }

    if (isActive) {
        return {
            circle: 'border-blue-600 bg-blue-600 text-white dark:border-blue-500 dark:bg-blue-600',
            icon: 'text-gray-500',
            text: 'text-blue-600 dark:text-blue-300',
            badge: 'border-blue-500 bg-blue-50 text-blue-600 dark:border-blue-400 dark:bg-blue-900 dark:text-blue-300',
        };
    }

    if (isUpcoming) {
        return {
            circle: 'border-gray-300 text-gray-400 dark:border-gray-600 dark:text-gray-500',
            icon: 'text-gray-400 dark:text-gray-500',
            text: 'text-gray-400 dark:text-gray-500',
            badge: 'border-gray-300 bg-gray-100 text-gray-400 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400',
        };
    }

    return {};
};

// Base payment step order (without failed by default)
const basePaymentStepOrder: PaymentStatus[] = [PaymentStatus.Pending, PaymentStatus.Approved];
const allPaymentSteps = [
    { value: PaymentStatus.Pending, label: 'Pending' },
    { value: PaymentStatus.Approved, label: 'Approved' },
    { value: PaymentStatus.Declined, label: 'Declined' },
    { value: PaymentStatus.Refunded, label: 'Refunded' },
    { value: PaymentStatus.Cancelled, label: 'Cancelled' },
];
const currentPaymentStatus = computed(() => props.order?.order_payment?.payment_status || PaymentStatus.Pending);

const visiblePaymentSteps = computed(() => {
    // Show only Declined if current is Declined
    if (currentPaymentStatus.value === PaymentStatus.Declined) {
        return paymentStepOrder
            .filter((s) => s === PaymentStatus.Pending || s === PaymentStatus.Declined)
            .map((value) => ({ value, label: value.charAt(0).toUpperCase() + value.slice(1).toLowerCase() }));
    }

    // Otherwise, exclude Declined and Cancelled (optional: handle cancelled separately)
    return paymentStepOrder
        .filter((s) => ![PaymentStatus.Declined, PaymentStatus.Cancelled].includes(s))
        .map((value) => ({ value, label: value.charAt(0).toUpperCase() + value.slice(1).toLowerCase() }));
});

// Order for step completion calculation
const paymentStepOrder: PaymentStatus[] = [
    PaymentStatus.Pending,
    PaymentStatus.Approved,
    PaymentStatus.Refunded,
    PaymentStatus.Declined,
    PaymentStatus.Cancelled,
];

// Step completion check
const isStepCompleted = (stepStatus: PaymentStatus): boolean => {
    const currentIndex = paymentStepOrder.indexOf(currentPaymentStatus.value);
    const stepIndex = paymentStepOrder.indexOf(stepStatus);
    return stepIndex < currentIndex;
};

// Step circle style
const getStepClasses = (stepStatus: PaymentStatus): string => {
    if (stepStatus === currentPaymentStatus.value) {
        return 'border-blue-500 bg-blue-500 text-white'; // active
    }
    if (isStepCompleted(stepStatus)) {
        return 'border-green-500 bg-green-500 text-white'; // completed
    }
    if (stepStatus === PaymentStatus.Declined && currentPaymentStatus.value === PaymentStatus.Declined) {
        return 'border-red-500 bg-red-500 text-white'; // declined
    }
    return 'border-gray-300 bg-white text-gray-500'; // pending
};

const getStepTextClasses = (stepStatus: PaymentStatus): string => {
    return isStepCompleted(stepStatus) || stepStatus === currentPaymentStatus.value ? 'text-foreground' : 'text-muted-foreground';
};

// 6. Actions / handlers
const updatePaymentStatus = () => {
    if (!props.order) return;

    form.put(route('orders.update', props.order.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast({
                variant: 'default',
                duration: 3000,
                description: trans('order.form.messages.updated_successfully'),
            });
        },
        onError: (error) => {
            toast({
                variant: 'destructive',
                duration: 3000,
                description: error?.message || trans('order.form.messages.update_failed'),
            });
        },
    });
};
const handleStatusUpdate = () => {
    if (!props.order) return;

    form.put(route('orders.update', props.order.id), {
        onSuccess: () => {
            toast({
                variant: 'default',
                duration: 3000,
                description: trans('order.form.messages.updated_successfully'),
            });
        },
        onError: (error) => {
            toast({
                variant: 'destructive',
                duration: 3000,
                description: error?.message || trans('order.form.messages.update_failed'),
            });
        },
    });
};

const updateOrderStatus = () => {
    if (!props.order) return;

    const currentIndex = statusOrder.indexOf(form.status as OrderStatus);

    if (currentIndex === -1 || currentIndex >= statusOrder.length - 1 || form.status === OrderStatus.CANCELLED) {
        return;
    }

    form.status = statusOrder[currentIndex + 1];

    handleStatusUpdate();
};

const downloadInvoice = () => {
    if (!props.order) return;
    window.open(route('orders.invoice', props.order.id), '_blank');
};

const handleUpload = () => {
    console.log('Upload payment proof...');
};

// Computed that depends on constants and form

const currentStepIndex = computed(() => {
    return orderSteps.value.findIndex((step) => step.status === form.status);
});

const progressWidth = computed(() => {
    const activeSteps = orderSteps.value.filter((step) => step.status !== OrderStatus.CANCELLED);
    const currentIndex = activeSteps.findIndex((step) => step.status === form.status);

    if (form.status === OrderStatus.CANCELLED) {
        const cancelledIndex = orderSteps.value.findIndex((step) => step.status === OrderStatus.CANCELLED);
        const total = orderSteps.value.length - 1;
        return (cancelledIndex / total) * 100;
    }

    if (currentIndex === -1) return 0;

    const total = activeSteps.length - 1;

    return (currentIndex / total) * 100;
});
</script>

<template>
    <div class="min-h-screen bg-background p-4 text-foreground md:p-6">
        <div class="w-full">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex items-center">
                        <h1 class="flex items-center gap-2 text-2xl font-semibold">
                            <ShoppingCart class="h-7 w-7" />
                            {{ trans('order.details.order') }} #{{ order?.order_number }}
                        </h1>
                    </div>

                    <Badge
                        :class="`${getStatusColor(order?.status ?? 'pending', 'order')} hover:bg-opacity-80 capitalize`"
                        class="w-fit"
                        variant="outline"
                    >
                        {{ order?.status }}
                    </Badge>

                    <span class="text-sm text-muted-foreground"> 📅 {{ order?.created_at ? formatDateTable(order.created_at) : '' }} </span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Order Items Section -->
                <div class="space-y-6 lg:col-span-2">
                    <Card>
                        <CardContent>
                            <!-- Heading -->
                            <div class="mb-8">
                                <h2 class="mb-2 text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ trans('order.tracking.title') }}
                                </h2>
                                <p class="text-gray-600 dark:text-gray-400">
                                    {{ trans('order.tracking.subtitle') }}
                                </p>
                            </div>

                            <!-- Order Steps -->
                            <div class="relative">
                                <!-- Track background line -->
                                <div class="absolute top-8 right-8 left-8 h-0.5 bg-gray-200 dark:bg-gray-700"></div>
                                <!-- Track progress line -->
                                <div
                                    class="absolute top-8 left-8 h-0.5 bg-blue-500 transition-all duration-500 ease-in-out"
                                    :style="{ width: `${progressWidth}%`, maxWidth: 'calc(100% - 64px)' }"
                                />

                                <!-- Step icons -->
                                <div class="relative flex items-start justify-between">
                                    <div v-for="(step, index) in orderSteps" :key="step.id" class="flex flex-1 flex-col items-center">
                                        <div
                                            class="relative z-10 flex h-16 w-16 items-center justify-center rounded-full border-4 bg-white transition-all duration-300 dark:bg-gray-900"
                                            :class="getStepStyles(step.status, index).circle"
                                        >
                                            <component
                                                :is="step.icon"
                                                class="h-7 w-7 transition-transform duration-300"
                                                :class="getStepStyles(step.status, index).icon"
                                            />
                                        </div>

                                        <div class="mt-4 max-w-32 text-center">
                                            <h3 class="mb-1 text-sm font-semibold" :class="getStepStyles(step.status, index).text">
                                                {{ step.title }}
                                            </h3>
                                            <span
                                                class="inline-flex rounded-full border px-2 py-1 text-xs font-medium"
                                                :class="getStepStyles(step.status, index).badge"
                                            >
                                                {{ trans(`order.status.${step.status}`) }}
                                            </span>
                                            <p v-if="step.timestamp" class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                                {{ step.timestamp }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cancelled Message -->
                            <div
                                v-if="form.status === OrderStatus.CANCELLED"
                                class="mt-6 text-center text-sm font-medium text-red-600 dark:text-red-400"
                            >
                                {{ trans('order.tracking.cancelled_message') }}
                            </div>

                            <!-- Order Details -->
                            <div class="mt-12 border-t border-gray-200 pt-8 dark:border-gray-700">
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                                        <h4 class="mb-2 font-semibold text-gray-900 dark:text-gray-100">
                                            {{ trans('order.tracking.order_information') }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ trans('order.tracking.order_number_label') }}: #{{ order?.order_number || '123456789' }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ trans('order.tracking.placed_label') }}:
                                            {{ order?.created_at ? formatDateTable(order.created_at) : '2025-07-15' }}
                                        </p>
                                    </div>

                                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                                        <h4 class="mb-2 font-semibold text-gray-900 dark:text-gray-100">
                                            {{ trans('order.details.shipping_address') }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ order?.address?.contact_name || '123 Main Street, Phnom Penh, Cambodia' }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ order?.address?.address }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ order?.address?.city }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ order?.address?.phone }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex flex-wrap justify-end gap-3">
                                <Button variant="default" @click="updateOrderStatus">
                                    <RefreshCw class="h-4 w-4" />
                                    {{ trans('order.tracking.update_status') }}
                                </Button>
                                <Button variant="default" @click="downloadInvoice">
                                    <Download class="h-4 w-4" />
                                    {{ trans('order.tracking.download_invoice') }}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="space-y-4 p-6">
                        <CardHeader>
                            <CardTitle class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-2">
                                    <Package class="h-5 w-5 text-yellow-500" />
                                    <span class="text-lg font-semibold">
                                        {{ trans('order.details.items') }}
                                    </span>
                                    <Badge variant="secondary">
                                        {{ order?.order_items?.length ?? 0 }}
                                    </Badge>
                                </div>
                            </CardTitle>
                        </CardHeader>

                        <CardContent class="space-y-2">
                            <!-- Table Header (hidden on mobile) -->
                            <div class="hidden items-center px-2 text-xs font-medium text-muted-foreground md:flex">
                                <div class="w-20 text-center">{{ trans('order.details.image') }}</div>
                                <div class="flex-1 text-left">{{ trans('order.details.product') }}</div>
                                <div class="w-28 text-center">{{ trans('order.details.unit_price') }}</div>
                                <div class="w-12 text-center">{{ trans('order.details.quantity') }}</div>
                                <div class="w-24 text-right">{{ trans('order.details.total') }}</div>
                            </div>

                            <!-- Items -->
                            <div v-if="order">
                                <div
                                    v-for="(item, index) in order.order_items"
                                    :key="index"
                                    class="rounded-lg px-2 py-4 transition-colors hover:bg-muted/50"
                                >
                                    <div class="flex flex-col gap-3 md:flex-row md:items-center">
                                        <!-- Image -->
                                        <div class="flex w-20 shrink-0 justify-center">
                                            <ImagePreview
                                                :image="item.image"
                                                alt="Product"
                                                class="h-12 w-12 rounded bg-muted object-cover shadow-sm"
                                            />
                                        </div>

                                        <!-- Product Details -->
                                        <div class="flex-1">
                                            <h3 class="text-sm font-medium">{{ item.name }}</h3>
                                            <p class="text-xs text-muted-foreground">
                                                {{ trans('order.details.modification') }}: {{ item.modification.modification_name }}
                                            </p>
                                            <p class="text-xs text-muted-foreground">
                                                {{ trans('order.details.type') }}: {{ item.modification.unit }}
                                            </p>
                                        </div>

                                        <!-- Unit Price -->
                                        <div class="w-28 text-right">
                                            <p v-if="item.compare_price" class="text-xs text-red-500 line-through">${{ item.compare_price }}</p>
                                            <p class="text-sm text-muted-foreground">
                                                {{ formatCurrency(item.modification.unit_price) }}
                                            </p>
                                        </div>

                                        <!-- Quantity -->
                                        <div class="w-12 text-center">
                                            <p class="text-sm">{{ item.quantity }}</p>
                                        </div>

                                        <!-- Total Price -->
                                        <div class="w-24 text-right">
                                            <p class="text-sm font-medium">
                                                {{ formatCurrency(item.total_price) }}
                                            </p>
                                        </div>
                                    </div>

                                    <Separator v-if="index !== order.order_items.length - 1" class="mt-4" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="space-y-4">
                            <CardTitle class="flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center gap-2">
                                    <CarFront class="h-5 w-5 text-primary" />
                                    <span> {{ trans('order.details.delivery') }} #{{ order?.delivery?.delivery_id }} </span>
                                    <!-- <Badge
                                        :class="`${getStatusColor(order?.delivery_status ?? 'pending', 'delivery')} hover:bg-opacity-80 capitalize`"
                                        class="w-fit"
                                        variant="outline"
                                    >
                                        {{ order?.delivery_status }}
                                    </Badge> -->
                                </div>
                            </CardTitle>
                        </CardHeader>

                        <CardContent>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center overflow-hidden rounded bg-muted">
                                        <ImagePreview v-if="order?.delivery?.image" :image="order.delivery.image" />
                                        <span v-else class="flex h-12 w-12 items-center justify-center rounded bg-muted text-xs text-muted-foreground"
                                            >N/A</span
                                        >
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ order?.delivery?.name || trans('order.details.unknown_carrier') }}</p>
                                        <p class="text-sm text-muted-foreground">
                                            {{ order?.delivery?.description || trans('order.details.no_delivery_info') }}
                                        </p>
                                    </div>
                                </div>
                                <p class="font-medium">{{ formatCurrency(order?.delivery?.shipping_fee ?? 0) }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>{{ trans('order.details.payment_summary') }}</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">
                                    {{ trans('order.details.subtotal') }} ({{ order?.order_items?.length }}
                                    {{ order?.order_items?.length === 1 ? trans('order.details.item') : trans('order.details.items') }})
                                </span>
                                <span>{{ formatCurrency(order?.total_price ?? 0) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">{{ trans('order.details.delivery') }}</span>
                                <span>{{ formatCurrency(order?.delivery_fee ?? 0) }}</span>
                            </div>
                            <Separator />
                            <div class="flex justify-between font-semibold">
                                <span>{{ trans('order.details.total_paid') }}</span>
                                <span>{{ formatCurrency(order?.total_amount ?? 0) }}</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <UserIcon class="h-5 w-5" />
                                {{ trans('order.details.customer') }} #{{ order?.user?.user_id }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="flex items-center gap-3">
                                <ImagePreview v-if="order?.user?.image" :image="order.user.image" />
                                <span v-else class="flex h-12 w-12 items-center justify-center rounded bg-muted text-xs text-muted-foreground"
                                    >N/A</span
                                >
                                <div>
                                    <p class="font-medium">
                                        {{
                                            order?.user?.first_name && order?.user?.last_name
                                                ? order.user.first_name + ' ' + order.user.last_name
                                                : trans('order.details.unknown_customer')
                                        }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Contact Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Phone class="h-5 w-5" />
                                {{ trans('order.details.contact_info') }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div v-if="order?.user?.email" class="flex items-center gap-3 text-sm">
                                <Mail class="h-4 w-4 text-muted-foreground" />
                                <span>{{ order.user.email }}</span>
                            </div>
                            <div v-if="order?.user?.phone" class="flex items-center gap-3 text-sm">
                                <Phone class="h-4 w-4 text-muted-foreground" />
                                <span>{{ order.user.phone }}</span>
                            </div>
                            <div v-if="order?.user?.telegram" class="flex items-center gap-3 text-sm">
                                <PhTelegramLogo :size="18" class="text-muted-foreground" />
                                <span>{{ order.user.telegram }}</span>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Payment Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <CreditCard class="h-5 w-5" />
                                {{ trans('order.details.payment_info') }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <!-- Payment Status Steps -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-medium text-muted-foreground">Payment Progress</h3>
                                <div class="flex items-center justify-between">
                                    <div
                                        v-for="(status, index) in visiblePaymentSteps"
                                        :key="status.value"
                                        class="flex items-center"
                                        :class="{ 'flex-1': index < visiblePaymentSteps.length - 1 }"
                                    >
                                        <!-- Step Circle + Label -->
                                        <div class="flex items-center">
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-full border-2 text-xs font-medium transition-colors"
                                                :class="getStepClasses(status.value, currentPaymentStatus)"
                                            >
                                                <CheckCircle
                                                    v-if="
                                                        isStepCompleted(status.value, currentPaymentStatus) || status.value === currentPaymentStatus
                                                    "
                                                    class="h-4 w-4"
                                                />
                                                <XCircle
                                                    v-else-if="
                                                        status.value === PaymentStatus.Declined && currentPaymentStatus === PaymentStatus.Declined
                                                    "
                                                    class="h-4 w-4"
                                                />
                                                <span v-else>{{ index + 1 }}</span>
                                            </div>
                                            <span
                                                class="ml-2 text-xs font-medium transition-colors"
                                                :class="getStepTextClasses(status.value, currentPaymentStatus)"
                                            >
                                                {{ status.label }}
                                            </span>
                                        </div>

                                        <!-- Connector Line -->
                                        <div
                                            v-if="index < visiblePaymentSteps.length - 1"
                                            class="mx-4 h-0.5 flex-1 transition-colors"
                                            :class="isStepCompleted(status.value, currentPaymentStatus) ? 'bg-green-500' : 'bg-gray-200'"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Current Status Badge -->
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex gap-4">
                                    <span class="text-sm font-medium">Current Status:</span>
                                    <Badge
                                        v-if="order?.order_payment?.payment_status"
                                        :class="`${getPaymentStatusColor(order.order_payment.payment_status)} hover:bg-opacity-80 capitalize`"
                                        class="w-fit"
                                        variant="outline"
                                    >
                                        {{ order.order_payment.payment_status }}
                                    </Badge>
                                </div>
                                <div>
                                    <Combobox
                                        v-model="form.payment_status"
                                        :items="visiblePaymentSteps"
                                        :empty-text="trans('item.form.no_company_found')"
                                        :placeholder="trans('item.form.select_company')"
                                        :default-label="trans('item.form.select')"
                                        :search="trans('item.form.search')"
                                        @update:model-value="updatePaymentStatus"
                                    />
                                </div>
                            </div>

                            <!-- Payment Details -->
                            <div class="space-y-2 text-sm text-muted-foreground">
                                <div v-if="order?.order_payment?.payment_method?.name">
                                    <span class="font-medium">{{ trans('order.details.method') }}:</span>
                                    {{ order.order_payment.payment_method.name }}
                                </div>
                                <div v-if="order?.order_payment?.paid_at && order?.order_payment?.payment_method?.type !== 'cash_on_delivery'">
                                    <span class="font-medium">{{ trans('order.details.paid_at') }}:</span>
                                    {{ formatDateTable(order.order_payment.paid_at) }}
                                </div>
                                <div v-if="order?.order_payment?.notes">
                                    <span class="font-medium">{{ trans('order.details.notes') }}:</span>
                                    {{ order.order_payment.notes }}
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <MapPinIcon class="h-5 w-5" />
                                {{ trans('order.details.shipping_address') }}
                            </CardTitle>
                        </CardHeader>

                        <CardContent>
                            <div class="space-y-1 text-sm">
                                <p class="font-medium">{{ order?.address?.contact_name }}</p>
                                <p>{{ order?.address?.phone }}</p>
                                <p>{{ order?.address?.city }}</p>
                                <p>{{ order?.address?.postal_code }}</p>
                                <p>{{ order?.address?.address }}</p>
                            </div>

                            <Separator class="my-4" />

                            <!-- Right aligned button -->
                            <div class="flex justify-end">
                                <Button variant="default" @click="router.visit(route('orders.index'))" class="flex items-center gap-2 px-8">
                                    {{ trans('order.button.back') }}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>
