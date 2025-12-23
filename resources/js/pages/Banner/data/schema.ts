import { z } from 'zod';
export const BannerSchema = z.object({
    id: z.string().uuid(),
    banner_id: z.string(),
    name: z.string(),
    banner_image: z.string(),
    description: z.string(),
    created_at: z.string(),
    updated_at: z.string(),
});
export type Banner = z.infer<typeof BannerSchema>;
