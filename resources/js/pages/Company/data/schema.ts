import { z } from 'zod';
export const CompanySchema = z.object({
    id: z.string().uuid(),
    company_id: z.string(),
    name_en: z.string(),
    name_km: z.string(),
    description_en: z.string(),
    description_km: z.string(),
    logo: z.string(),
    delete_at: z.string(),
    created_at: z.string(),
    updated_at: z.string(),
});
export type Company = z.infer<typeof CompanySchema>;
