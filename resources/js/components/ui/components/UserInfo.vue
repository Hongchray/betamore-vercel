<script setup lang="ts">
import { useAuth } from '@/composables/useAuth';
import { useInitials } from '@/composables/useInitials';
import type { Admin } from '@/types';
import { computed } from 'vue';

interface Props {
    admin?: Admin;
    showEmail?: boolean;
    useGlobalUser?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
    useGlobalUser: false,
});

const { getInitials } = useInitials();
const { user: globalUser } = useAuth();

// Priority: explicit admin prop > global user (if enabled) > null
const displayUser = computed(() => {
    if (props.admin) return props.admin;
    if (props.useGlobalUser && globalUser.value) return globalUser.value;
    return null;
});
</script>

<template>
    <div v-if="displayUser" class="flex items-center space-x-2">
        <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
            <AvatarImage v-if="displayUser.image" :src="displayUser.image" :alt="displayUser.name" />
            <AvatarFallback class="rounded-lg text-black dark:text-white">
                {{ getInitials(displayUser.name) }}
            </AvatarFallback>
        </Avatar>

        <div class="grid flex-1 text-left text-sm leading-tight">
            <span class="truncate font-medium">{{ displayUser.name }}</span>
            <span v-if="showEmail && displayUser.email" class="text-muted-foreground truncate text-xs">
                {{ displayUser.email }}
            </span>
        </div>
    </div>
    <div v-else class="flex items-center space-x-2">
        <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
            <AvatarFallback class="rounded-lg text-black dark:text-white"> -- </AvatarFallback>
        </Avatar>
        <div class="grid flex-1 text-left text-sm leading-tight">
            <span class="text-muted-foreground truncate font-medium">No user data</span>
        </div>
    </div>
</template>
