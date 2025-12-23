// utils/formatTime.ts

export function formatTime(dateInput: Date | string | number | null | undefined, mode: '12h' | '24h' = '24h', locale: string = 'en-US'): string {
    if (!dateInput) return '-';

    let date: Date;

    if (typeof dateInput === 'string' && /^\d{2}:\d{2}:\d{2}$/.test(dateInput)) {
        // Handle time-only format like "09:59:58"
        const today = new Date().toISOString().split('T')[0]; // YYYY-MM-DD
        date = new Date(`${today}T${dateInput}`);
    } else {
        date = new Date(dateInput);
    }

    if (isNaN(date.getTime())) {
        console.warn('Invalid date input for formatTime:', dateInput);
        return '-';
    }

    const options: Intl.DateTimeFormatOptions = {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: mode === '12h',
    };

    return new Intl.DateTimeFormat(locale, options).format(date);
}
