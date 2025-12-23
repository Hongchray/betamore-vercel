import { z } from 'zod';
export const permissionSchema = z.object({
    id: z.string(),
    name: z.string(),
    guard_name: z.string(),
    created_at: z.string(),
    updated_at: z.string(),
});
const roleSchema = z.object({
    id: z.number(),
    name: z.string(),
    guard_name: z.string(),
    created_at: z.string(),
    updated_at: z.string(),
    permissions: z.array(permissionSchema),
});
export const UserSchema = z.object({
    id: z.string().uuid(),
    user_id: z.string(),
    first_name: z.string(),
    last_name: z.string(),
    gender: z.enum(['male', 'female', 'other']),
    email: z.string(), // nullable in DB
    phone: z.string(),
    telegram: z.string(), // nullable in DB
    type: z.enum(['admin', 'customer']), // maps to enum in DB
    address: z.string(),
    image: z.string(),
    otp_code: z.string().nullable(),
    phone_verified_at: z.string().nullable(),
    otp_expires_at: z.string().nullable(),
    email_verified_at: z.string().nullable(),
    password: z.string(), // usually omitted in responses
    password_confirmation: z.string().optional(), // used for form validation
    remember_token: z.string().nullable().optional(), // Laravel default
    created_at: z.string(),
    updated_at: z.string(),

    // Optional/relational fields (not in DB directly)
    role_id: z.number().optional(), // not in users table yet
    get_roles: roleSchema.optional(),
    roles: z.array(roleSchema).optional(),
});

export type User = z.infer<typeof UserSchema>;
