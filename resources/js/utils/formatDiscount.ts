// utils/formatDiscount.ts

export type DiscountType = 'percent' | 'flat';

export interface DiscountValue {
    value: number;
    type: DiscountType;
}

/**
 * Formats the discount value:
 * - If type is 'percent', appends '%'
 * - If type is 'fixed', formats as USD currency with '$'
 */
export function formatDiscount(discount: DiscountValue): string {
    if (discount.type === 'percent') {
        return `${discount.value}%`;
    }

    if (discount.type === 'flat') {
        // Format as USD currency
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(discount.value);
    }

    return discount.value.toString();
}
