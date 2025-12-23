import { z } from 'zod';
export const reportSchema = z.object({
    id: z.string(),
    item_id: z.string(),
    item_name_en: z.string(),
    item_name_km: z.string(),
    modification_id: z.string(),
    modification_name: z.string(),
    order_count: z.string(),
    total_quantity_ordered: z.string(),
    total_revenue: z.string(),
    unit: z.string(),
    unit_price: z.string(),
});

export type Report = z.infer<typeof reportSchema>;
