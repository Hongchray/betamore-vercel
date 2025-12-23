import { getActiveLanguage, trans } from 'laravel-vue-i18n';

export type DateFormatOptions = {
    locale?: string;
    options?: Intl.DateTimeFormatOptions;
    useTranslations?: boolean;
    format?: 'short' | 'long' | 'table' | 'custom';
};

/**
 * Format date using Laravel translations
 */
function formatDateWithTranslations(date: Date, format: string = 'table'): string {
    try {
        const day = date.getDate();
        const month = date.getMonth() + 1;
        const year = date.getFullYear();
        const hour24 = date.getHours();
        const minute = date.getMinutes().toString().padStart(2, '0');
        const weekday = date.getDay();

        // Convert to 12-hour format
        const hour12 = hour24 === 0 ? 12 : hour24 > 12 ? hour24 - 12 : hour24;
        const ampm = hour24 >= 12 ? 'PM' : 'AM';
        const hour12Str = hour12.toString().padStart(2, '0');
        const hour24Str = hour24.toString().padStart(2, '0');

        // Get translated month and weekday
        const monthName = trans(`date.months.${month}`);
        const monthShort = trans(`date.months_short.${month}`);
        const weekdayName = trans(`date.weekdays.${weekday}`);
        const weekdayShort = trans(`date.weekdays_short.${weekday}`);

        // Get format pattern
        const formatPattern = trans(`date.formats.${format}`);

        // Pattern replacement with support for new format codes
        // Use regex with word boundaries to avoid conflicts
        let formatted = formatPattern
            .replace(/\bD\b/g, weekdayShort) // Short weekday name
            .replace(/\bl\b/g, weekdayName) // Full weekday name
            .replace(/\bF\b/g, monthName) // Full month name
            .replace(/\bM\b/g, monthShort) // Short month name
            .replace(/\bj\b/g, day.toString()) // Day without leading zero
            .replace(/\bd\b/g, day.toString().padStart(2, '0')) // Day with leading zero
            .replace(/\bn\b/g, month.toString()) // Month without leading zero
            .replace(/\bm\b/g, month.toString().padStart(2, '0')) // Month with leading zero
            .replace(/\bY\b/g, year.toString()) // 4-digit year
            .replace(/\bH\b/g, hour24Str) // 24-hour format
            .replace(/\bh\b/g, hour12Str) // 12-hour format with leading zero
            .replace(/\bg\b/g, hour12.toString()) // 12-hour format without leading zero
            .replace(/\bi\b/g, minute) // Minutes
            .replace(/\bA\b/g, ampm); // AM/PM

        return formatted;
    } catch (error) {
        console.error('Translation date formatting error:', error);
        return date.toLocaleDateString();
    }
}

function isLocaleSupported(locale: string): boolean {
    try {
        new Intl.DateTimeFormat(locale);
        return true;
    } catch {
        return false;
    }
}

/**
 * Get supported locale with fallbacks
 */
function getSupportedLocale(preferredLocale: string): string {
    if (isLocaleSupported(preferredLocale)) {
        return preferredLocale;
    }

    const fallbacks: Record<string, string[]> = {
        km: ['km', 'km-KH', 'en-US'],
        zh: ['zh-CN', 'zh', 'en-US'],
        en: ['en-US', 'en'],
    };

    const langCode = preferredLocale.split('-')[0];
    const fallbackList = fallbacks[langCode] || ['en-US'];

    for (const fallback of fallbackList) {
        if (isLocaleSupported(fallback)) {
            return fallback;
        }
    }

    return 'en-US';
}

function getCurrentLocale(): string {
    const activeLanguage = getActiveLanguage();

    const localeMap: Record<string, string> = {
        en: 'en-US',
        km: 'km-KH',
        zh: 'zh-CN',
        'zh-cn': 'zh-CN',
    };

    const preferredLocale = localeMap[activeLanguage] || 'en-US';
    return getSupportedLocale(preferredLocale);
}

export function formatDate(date: Date | string, config: DateFormatOptions = {}): string {
    const { locale = getCurrentLocale(), options = {}, useTranslations = true, format = 'table' } = config;

    const dt = typeof date === 'string' ? new Date(date) : date;
    if (isNaN(dt.getTime())) return 'Invalid Date';

    // Use Laravel translations if enabled
    if (useTranslations) {
        try {
            return formatDateWithTranslations(dt, format);
        } catch (error) {
            console.error('Translation formatting failed, falling back to Intl:', error);
        }
    }

    // Fallback to Intl.DateTimeFormat
    const supportedLocale = getSupportedLocale(locale);
    const defaultOptions: Intl.DateTimeFormatOptions = {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true, // Changed to 12-hour format
        ...options,
    };

    try {
        return new Intl.DateTimeFormat(supportedLocale, defaultOptions).format(dt);
    } catch (error) {
        console.error('Date formatting error:', error);
        return new Intl.DateTimeFormat('en-US', defaultOptions).format(dt);
    }
}

// Convenience functions
export function formatDateShort(date: Date | string): string {
    return formatDate(date, { format: 'short', useTranslations: true });
}

export function formatDateLong(date: Date | string): string {
    return formatDate(date, { format: 'long', useTranslations: true });
}

export function formatDateTable(date: Date | string): string {
    return formatDate(date, { format: 'table', useTranslations: true });
}
