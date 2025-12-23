import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useTranslations() {
    const page = usePage();

    const translations = computed(() => page.props.translations as Record<string, string>);
    const locale = computed(() => page.props.locale as string);
    const availableLocales = computed(() => page.props.available_locales as Record<string, string>);

    const __ = (key: string, fallback?: string): string => {
        return translations.value[key] || fallback || key;
    };

    return {
        __,
        locale,
        availableLocales,
        translations,
    };
}
