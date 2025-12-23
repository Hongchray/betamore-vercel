<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavSection } from '@/interfaces/SideBar';
import { Link } from '@inertiajs/vue3';

interface NavMainProps {
    sections: NavSection[];
}

defineProps<NavMainProps>();
</script>

<template>
    <SidebarGroup v-for="section in sections" :key="section.title">
        <SidebarGroupLabel>{{ section.title }}</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in section.items" :key="item.title">
                <SidebarMenuButton as-child>
                    <Link :href="item.href">
                        <component v-if="item.icon" :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
