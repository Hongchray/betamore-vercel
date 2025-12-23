<template>
    <Badge :class="orderStatusColors[status]" :variant="variant" class="capitalize">
        <component v-if="showIcon" :is="getStatusIcon(status)" class="mr-1 h-3 w-3" />
        {{ formatStatus(status) }}
    </Badge>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { OrderStatus } from '@/enums/OrderStatus';
import { orderStatusColors } from '@/utils/statusColors';
import { CheckCircle, CheckCircle2, Clock, Package, Truck, XCircle } from 'lucide-vue-next';

interface Props {
    status: keyof typeof orderStatusColors;
    variant?: 'default' | 'secondary' | 'destructive' | 'outline';
    showIcon?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'outline',
    showIcon: false,
});

const getStatusIcon = (status: OrderStatus) => {
    const icons: Record<OrderStatus, any> = {
        [OrderStatus.IN_CART]: Clock,
        [OrderStatus.PENDING]: Clock,
        [OrderStatus.CONFIRMED]: CheckCircle,
        [OrderStatus.PROCESSING]: Package,
        [OrderStatus.SHIPPED]: Truck,
        [OrderStatus.DELIVERED]: CheckCircle2,
        [OrderStatus.CANCELLED]: XCircle,
    };

    return icons[status] || Clock;
};

const formatStatus = (status: string) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};
</script>
