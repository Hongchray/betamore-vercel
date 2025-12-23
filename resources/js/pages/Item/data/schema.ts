export interface Company {
    id: string;
    name_en: string;
    logo: string;
    created_at?: string;
    updated_at?: string;
}

export interface ItemModification {
    id: string | null;
    item_id?: string;
    modification_name: string;
    unit: string;
    modification: number;
    created_at?: string;
    updated_at?: string;
}

export interface Item {
    id: string;
    item_id: string;
    name_en: string;
    name_km: string;
    description_en: string;
    description_km: string;
    company_id: string;
    company?: Company;
    created_at: string;
    updated_at: string;
    deleted_at?: string | null;
}
