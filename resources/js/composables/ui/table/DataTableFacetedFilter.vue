<script setup lang="ts">
import { CheckIcon, PlusCircledIcon } from '@radix-icons/vue';
import type { Column } from '@tanstack/vue-table';
import type { Component } from 'vue';
import { computed } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList, CommandSeparator } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Separator } from '@/components/ui/separator';
import { cn } from '@/lib/utils';

interface DataTableFacetedFilter {
    column?: Column<any, any>;
    title?: string;
    options: {
        label: string;
        value: string;
        icon?: Component;
    }[];
}

const props = defineProps<DataTableFacetedFilter>();

const facets = computed(() => props.column?.getFacetedUniqueValues());
const selectedValues = computed(() => {
    const filterValue = props.column?.getFilterValue();
    return new Set(Array.isArray(filterValue) ? filterValue : filterValue ? [filterValue] : []);
});

const filterFunction = (val: any[], term: string): any[] => {
    if (!Array.isArray(val)) return [];

    return val.filter((item) => {
        if (typeof item === 'object' && item !== null && 'label' in item) {
            return item.label.toLowerCase().includes(term.toLowerCase());
        }
        return String(item).toLowerCase().includes(term.toLowerCase());
    });
};

// Add safe options computed property
const safeOptions = computed(() => {
    return Array.isArray(props.options) ? props.options : [];
});

// Add safe title computed property
const safeTitle = computed(() => {
    return props.title || 'Filter';
});
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button variant="outline" size="sm" class="h-8 border-dashed">
                <PlusCircledIcon class="mr-2 h-4 w-4" />
                {{ safeTitle }}
                <template v-if="selectedValues.size > 0">
                    <Separator orientation="vertical" class="mx-2 h-4" />
                    <Badge variant="secondary" class="rounded-sm px-1 font-normal lg:hidden">
                        {{ selectedValues.size }}
                    </Badge>
                    <div class="hidden space-x-1 lg:flex">
                        <Badge v-if="selectedValues.size > 2" variant="secondary" class="rounded-sm px-1 font-normal">
                            {{ selectedValues.size }} selected
                        </Badge>

                        <template v-else>
                            <Badge
                                v-for="option in safeOptions.filter((option) => selectedValues.has(option.value))"
                                :key="option.value"
                                variant="secondary"
                                class="rounded-sm px-1 font-normal"
                            >
                                {{ option.label }}
                            </Badge>
                        </template>
                    </div>
                </template>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto max-w-[300px] p-0" align="start">
            <Command :filter-function="filterFunction">
                <CommandInput :placeholder="safeTitle" />
                <CommandList>
                    <CommandEmpty>No results found.</CommandEmpty>
                    <CommandGroup>
                        <CommandItem
                            v-for="option in safeOptions"
                            :key="option.value"
                            :value="option"
                            @select="
                                () => {
                                    const isSelected = selectedValues.has(option.value);
                                    const currentValues = Array.from(selectedValues);

                                    let newValues;
                                    if (isSelected) {
                                        newValues = currentValues.filter((val) => val !== option.value);
                                    } else {
                                        newValues = [...currentValues, option.value];
                                    }

                                    column?.setFilterValue(newValues.length ? newValues : undefined);
                                }
                            "
                        >
                            <div
                                :class="
                                    cn(
                                        'mr-2 flex h-4 w-4 items-center justify-center rounded-sm border border-primary',
                                        selectedValues.has(option.value) ? 'bg-primary text-primary-foreground' : 'opacity-50 [&_svg]:invisible',
                                    )
                                "
                            >
                                <CheckIcon :class="cn('h-4 w-4')" />
                            </div>
                            <component :is="option.icon" v-if="option.icon" class="mr-2 h-4 w-4 text-muted-foreground" />
                            <span>{{ option.label }}</span>
                        </CommandItem>
                    </CommandGroup>

                    <template v-if="selectedValues.size > 0">
                        <CommandSeparator />
                        <CommandGroup>
                            <CommandItem
                                :value="{ label: 'Clear filters' }"
                                class="justify-center text-center"
                                @select="column?.setFilterValue(undefined)"
                            >
                                Clear filters
                            </CommandItem>
                        </CommandGroup>
                    </template>
                </CommandList>
            </Command>
        </PopoverContent>
    </Popover>
</template>
