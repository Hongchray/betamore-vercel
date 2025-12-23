import { z } from 'zod';

export const deliverySchema = z.object({
    id: z.string().uuid(),
    delivery_id: z.string(),
    name: z.string(),
    image: z.string(),
    shipping_fee: z.string(),
    description: z.string(),
    is_active: z.boolean(),
    created_at: z.string(),
    updated_at: z.string(),
});

export type Delivery = z.infer<typeof deliverySchema>;
