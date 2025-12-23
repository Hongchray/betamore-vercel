import { PaymentStatus } from '@/enums/PaymentStatus';
import { UserSchema } from '@/pages/Customer/data/schema';
import { z } from 'zod';
export const addressSchema = z.object({
    contact_name: z.string(),
    address: z.string(),
    user_id: z.string(),
    city: z.string(),
    postal_code: z.string(),
    phone: z.string(),
    lattitude: z.string(),
    longitude: z.string(),
});

export const PaymentMethodSchema = z.object({
    id: z.string().uuid(),
    name: z.string(),
    type: z.enum(['aba_payway', 'credit_card', 'cash_on_delivery']),
    description: z.string().nullable(),
    logo: z.string().url().nullable(),
    is_active: z.boolean(),
    created_at: z.string(),
    updated_at: z.string(),
});

export const DeliverySchema = z.object({
    id: z.string().uuid(),
    delivery_id: z.string(), // e.g., "DID0000000003"
    name: z.string(),
    image: z.string().url().nullable(), // Some deliveries might not have an image
    shipping_fee: z.string(), // Or use z.number() if you parse it as a number
    description: z.string().nullable(),
    is_active: z.union([z.literal(0), z.literal(1)]), // stored as integer flag
    created_at: z.string(), // ISO datetime string
    updated_at: z.string(),
});

export const OrderPaymentSchema = z.object({
    id: z.string().uuid(),
    order_id: z.string().uuid(),
    payment_method_id: z.string().uuid(),
    payment_status: z.enum([PaymentStatus.Approved, PaymentStatus.Cancelled, PaymentStatus.Declined, PaymentStatus.Pending, PaymentStatus.Refunded]),
    amount: z.string(), // Or use z.number().transform(n => n.toFixed(2)) if you want formatted output
    paid_at: z.string().nullable(),
    bank_id: z.string().uuid().nullable(),
    card_id: z.string().uuid().nullable(),
    notes: z.string().nullable(),
    created_at: z.string(),
    updated_at: z.string(),
    payment_method: PaymentMethodSchema,
});

export const orderSchema = z.object({
    id: z.string().uuid(),
    order_number: z.string(),
    user: UserSchema.optional(),
    order_payment: OrderPaymentSchema,
    delivery_fee: z.number().nullable(),
    total_amount: z.number(),
    order_items: z.array(z.any()),
    status: z.string().default('pending'),
    payment_status: z.string().default(PaymentStatus.Pending),
    payment_method: PaymentMethodSchema.optional(),
    address: addressSchema.optional(),
    total_price: z.number(),
    delivery: DeliverySchema,
    deleted_at: z.string().nullable().optional(), // for softDeletes timestamp
    note: z.string(),
    created_at: z.string(),
    updated_at: z.string(),
});

export const paymentSchema = z.object({
    id: z.string().uuid(),
    order: orderSchema,
    payment_status: z.string(),
    payment_method: PaymentMethodSchema,
    created_at: z.string(),
    updated_at: z.string(),
});

export type Payment = z.infer<typeof paymentSchema>;
