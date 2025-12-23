import { z } from 'zod';

export const PromotionSchema = z.object({
    id: z.string().uuid(),
    banner: z.string(),
    promotion_id: z.string().uuid().optional(), // optional if needed
    name_en: z.string(),
    name_km: z.string(),
    description_en: z.string().nullable().optional(),
    description_km: z.string().nullable().optional(),
    type: z.enum(['percent', 'flat']),
    discount_value: z.number(),
    start_date: z.string().nullable().optional(),
    end_date: z.string().nullable().optional(),
    start_time: z.string().nullable().optional(),
    end_time: z.string().nullable().optional(),
    items: z.array(z.string()), // replace z.string() with the correct schema if items are objects
});
export type Promotion = z.infer<typeof PromotionSchema>;
