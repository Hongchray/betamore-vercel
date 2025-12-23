<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { CaretSortIcon, CheckIcon, MagnifyingGlassIcon } from '@radix-icons/vue';
import { LoaderCircle } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { route } from 'ziggy-js';

interface ComboboxItem {
    value: string;
    label: string;
}

interface Props {
    modelValue?: string | number | null;
    items: ComboboxItem[];
    search?: string;
    placeholder?: string;
    emptyText?: string;
    buttonClass?: string;
    contentClass?: string;
    inputClass?: string;
    serverSideSearch?: boolean;
    disabled?: boolean;
    defaultLabel?: string;
    route?: string;
    id?: string | number;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Search...',
    emptyText: 'Nothing found.',
    buttonClass: 'w-full min-w-[150px]',
    contentClass: '',
    inputClass: 'h-9',
    serverSideSearch: false,
    defaultLabel: '-- Select --',
    route: '',
    id: '',
    search: '',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value?: number): void;
}>();

const open = ref(false);
const searchQuery = ref('');
const itemsData = ref<ComboboxItem[]>([]); // Store the filtered
watch(
    () => props.items,
    (newItems) => {
        if (!props.serverSideSearch) {
            itemsData.value = newItems;
        }
    },
    { immediate: true },
);
// Watch the search query and send it to the backend if server-side search is enabled
const loading = ref(false);
let debounceTimeout: ReturnType<typeof setTimeout> | null = null;

watch(
    searchQuery,
    (query) => {
        if (!props.serverSideSearch) return;

        // Clear previous timeout if still pending
        if (debounceTimeout) clearTimeout(debounceTimeout);

        // Set new debounce timeout
        debounceTimeout = setTimeout(async () => {
            loading.value = true;
            try {
                const response = await fetch(`${route(props.route)}?query=${encodeURIComponent(query)}&id=${props.id ?? ''}`);
                if (response.ok) {
                    const data = await response.json();
                    itemsData.value = data;
                } else {
                    console.error('Error fetching items:', response.statusText);
                }
            } catch (error) {
                console.error('Error fetching items:', error);
            } finally {
                loading.value = false;
            }
        }, 1000);
    },
    { immediate: true },
);

// Update selected label when modelValue changes
const selectedLabel = computed(() => {
    const selected = itemsData.value.find((item) => item.value == props.modelValue);
    return selected ? selected.label : props.defaultLabel || props.placeholder || '-- Select --';
});

// Handle item selection
const handleSelect = (value: any) => {
    emit('update:modelValue', value);
    open.value = false;
};
</script>

<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child :disabled="disabled">
            <Button
                variant="outline"
                role="combobox"
                :aria-expanded="open"
                :class="[buttonClass, 'justify-between disabled:bg-gray-100 disabled:font-bold disabled:opacity-70']"
            >
                {{ selectedLabel }}
                <CaretSortIcon class="ml-2 h-4 w-4 shrink-0 opacity-50" />
            </Button>
        </PopoverTrigger>
        <PopoverContent :class="[contentClass, 'p-0']">
            <Command>
                <!-- <Input v-if="serverSideSearch" :class="inputClass" placeholder="" /> -->
                <div class="flex items-center border-b px-3" cmdk-input-wrapper="" v-if="serverSideSearch">
                    <MagnifyingGlassIcon class="mr-2 h-4 w-4 shrink-0 opacity-50" />
                    <input
                        ref="inputRef"
                        :class="[
                            inputClass,
                            'flex w-full rounded-md bg-transparent text-sm outline-none placeholder:text-muted-foreground disabled:cursor-not-allowed',
                        ]"
                        placeholder="Search..."
                        v-model="searchQuery"
                    />
                </div>
                <CommandInput v-if="!serverSideSearch" :class="inputClass" :placeholder="props.search" />
                <CommandEmpty v-if="!loading">{{ props.emptyText || 'Nothing found.' }}</CommandEmpty>
                <CommandList v-if="!loading">
                    <CommandGroup>
                        <CommandItem v-for="item in itemsData" :key="item.value" :value="item.label" @select="handleSelect(item.value)">
                            {{ item.label }}
                            <CheckIcon :class="cn('ml-auto h-4 w-4', modelValue === item.value ? 'opacity-100' : 'opacity-0')" />
                        </CommandItem>
                    </CommandGroup>
                </CommandList>
                <div v-else class="flex items-center gap-2 p-2 text-center">
                    <LoaderCircle class="animate-spin text-primary" />
                    <span>loading..</span>
                </div>
            </Command>
        </PopoverContent>
    </Popover>
</template>
