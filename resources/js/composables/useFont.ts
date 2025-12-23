import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, watch } from 'vue';

export function useFont() {
    const page = usePage();

    const locale = computed(() => page.props.locale as string);

    const fontConfig = computed(() => {
        const fontMap: Record<string, string> = {
            en: 'Inter, sans-serif', // Changed from Roboto
            km: 'Kantumruy Pro, Khmer OS, sans-serif',
            zh: 'Noto Sans SC, PingFang SC, sans-serif',
        };

        return fontMap[locale.value] || fontMap['en'];
    });

    const fontClass = computed(() => `font-locale-${locale.value}`);

    const updateDocumentFont = () => {
        document.documentElement.style.setProperty('--font-family', fontConfig.value);

        // Remove existing locale font classes
        document.body.classList.forEach((className) => {
            if (className.startsWith('font-locale-')) {
                document.body.classList.remove(className);
            }
        });

        // Add current locale font class
        document.body.classList.add(fontClass.value);
    };

    onMounted(() => {
        updateDocumentFont();
    });

    watch(locale, () => {
        updateDocumentFont();
    });

    return {
        locale,
        fontConfig,
        fontClass,
        updateDocumentFont,
    };
}
