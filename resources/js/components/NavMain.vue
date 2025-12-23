<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';

import { type NavSection } from '@/interfaces/SideBar';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentPath = computed(() => page.url);

interface NavMainProps {
    sections: NavSection[];
}

defineProps<NavMainProps>();
</script>

<template>
    <SidebarGroup v-for="section in sections" :key="section.title">
        <SidebarGroupLabel class="text-gray-500 dark:text-gray-400">
            {{ section.title }}
        </SidebarGroupLabel>

        <SidebarMenu>
            <SidebarMenuItem v-for="item in section.items" :key="item.title">
                <SidebarMenuButton
                    :class="[
                        'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800',
                        currentPath.startsWith(item.href) && item.href !== '/'
                            ? 'bg-gray-100 font-bold text-primary dark:bg-gray-800 dark:text-white'
                            : '',
                    ]"
                    as-child
                >
                    <Link :href="item.href" class="flex items-center gap-2">
                        <component v-if="item.icon" :is="item.icon" class="h-4 w-4" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
