import { DeliveryStatus } from '@/enums/DeliveryStatus';
import { OrderStatus } from '@/enums/OrderStatus';
import { PaymentStatus } from '@/enums/PaymentStatus';

export const orderStatusColors: Record<OrderStatus, string> = {
    IN_CART: 'bg-orange-100 text-orange-800 border-orange-300',
    PENDING: 'bg-yellow-100 text-yellow-800 border-yellow-300',
    CONFIRMED: 'bg-blue-100 text-blue-800 border-blue-300',
    PROCESSING: 'bg-indigo-100 text-indigo-800 border-indigo-300',
    SHIPPED: 'bg-orange-100 text-orange-800 border-orange-300',
    DELIVERED: 'bg-green-100 text-green-800 border-green-300',
    CANCELLED: 'bg-red-100 text-red-800 border-red-300',
};

export const deliveryStatusColors: Record<DeliveryStatus, string> = {
    PENDING: 'bg-yellow-100 text-yellow-800 border-yellow-300',
    PICKED_UP: 'bg-blue-100 text-blue-800 border-blue-300',
    IN_TRANSIT: 'bg-indigo-100 text-indigo-800 border-indigo-300',
    DELIVERED: 'bg-green-100 text-green-800 border-green-300',
    FAILED: 'bg-red-100 text-red-800 border-red-300',
};

export const paymentStatusColors: Record<PaymentStatus, string> = {
    PENDING: 'bg-orange-100 text-orange-800 border-orange-300',
    APPROVED: 'bg-green-100 text-green-900 border-green-400',
    DECLINED: 'bg-red-100 text-red-900 border-red-400',
    REFUNDED: 'bg-blue-100 text-blue-900 border-blue-400',
    CANCELLED: 'bg-gray-100 text-gray-900 border-gray-400',
};
