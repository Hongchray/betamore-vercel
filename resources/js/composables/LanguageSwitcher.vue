<template>
    <DropdownMenu>
        <DropdownMenuTrigger
            class="flex items-center space-x-1 rounded-md border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
        >
            <!-- Try flag emoji with fallback -->
            <span class="text-lg leading-none" style="font-family: 'Apple Color Emoji', 'Segoe UI Emoji', 'Noto Color Emoji', sans-serif">
                {{ getLocaleFlag(currentLocale) }}
            </span>
            <span>{{ availableLocales[currentLocale] }}</span>
            <ChevronDownIcon class="h-4 w-4" />
        </DropdownMenuTrigger>

        <DropdownMenuContent class="w-48">
            <DropdownMenuItem
                v-for="(name, code) in availableLocales"
                :key="code"
                as="a"
                :href="`/language/switch/${code}`"
                :class="[
                    'flex cursor-pointer items-center space-x-3',
                    currentLocale === code ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white' : 'text-gray-700 dark:text-gray-300',
                ]"
            >
                <span class="text-lg leading-none" style="font-family: 'Apple Color Emoji', 'Segoe UI Emoji', 'Noto Color Emoji', sans-serif">
                    {{ getLocaleFlag(code) }}
                </span>
                <span>{{ name }}</span>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>

<script setup lang="ts">
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { usePage } from '@inertiajs/vue3';
import { ChevronDownIcon } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale as string);
const availableLocales = computed(() => page.props.available_locales as Record<string, string>);

const getLocaleFlag = (locale: string) => {
    const flagMap: Record<string, string> = {
        en: '🇺🇸',
        km: '🇰🇭',
        zh: '🇨🇳',
    };

    return flagMap[locale] || '🌐';
};
</script>
