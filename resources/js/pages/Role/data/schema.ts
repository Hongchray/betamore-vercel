import { z } from 'zod';

// Role schema
export const roleSchema = z.object({
    id: z.number(),
    name: z.string(),
    description: z.string().nullable(),
    guard_name: z.string(),
    created_at: z.string(),
    updated_at: z.string(),
});

// Permission schema
export const permissionSchema = z.object({
    id: z.number(),
    name: z.string(),
    display_name: z.string().nullable(),
    description: z.string().nullable(),
    category: z.string().nullable(),
    guard_name: z.string(),
    created_at: z.string(),
    updated_at: z.string(),
});

// Form validation schema
export const roleFormSchema = z.object({
    name: z.string().min(1, 'Role name is required').max(255, 'Role name is too long'),
    description: z.string().nullable(),
    permissions: z.array(z.number()).min(1, 'At least one permission is required'),
});

// Types
export type Role = z.infer<typeof roleSchema>;
export type Permission = z.infer<typeof permissionSchema>;
export type RoleFormData = z.infer<typeof roleFormSchema>;

// Role with permissions
export interface RoleWithPermissions extends Role {
    permissions: Permission[];
}
