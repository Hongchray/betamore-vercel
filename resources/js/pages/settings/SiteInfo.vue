<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import Textarea from '@/components/ui/components/ui/textarea/Textarea.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useToast } from '@/components/ui/toast/use-toast';
import UploadImage from '@/composables/ui/upload-image/UploadImage.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { FileText, Globe, Hash, Image, Loader2 } from 'lucide-vue-next';
import { computed } from 'vue';

interface SiteInfoProps {
    siteInfo: {
        site_name: string;
        logo_url?: string;
        favicon_url?: string;
        meta_description?: string;
        prefix?: Record<string, string>;
    };
}

const page = usePage();
const props = page.props as unknown as SiteInfoProps;
const { toast } = useToast();

const breadcrumbs = computed((): BreadcrumbItem[] => [
    {
        title: 'Site Settings',
        href: '/settings/site',
    },
]);

const form = useForm({
    site_name: props.siteInfo?.site_name ?? '',
    logo_url: props.siteInfo?.logo_url ?? '',
    favicon_url: props.siteInfo?.favicon_url ?? '',
    meta_description: props.siteInfo?.meta_description ?? '',
    prefix: props.siteInfo?.prefix ?? {},
});

const submit = () => {
    form.patch(route('website.update'), {
        preserveScroll: true,
        onSuccess: () => {
            toast({
                variant: 'default',
                duration: 3000,
                description: trans('setting.site_info.success_message'),
            });
        },
        onError: (error) => {
            toast({
                variant: 'destructive',
                duration: 3000,
                description: error?.message || trans('setting.site_info.error_message'),
            });
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Website Settings" />
        <SettingsLayout class="w-full">
            <div class="max-w-4xl">
                <div class="rounded-lg border border-border bg-background shadow-sm dark:border-zinc-700">
                    <div class="border-b border-border p-6 dark:border-zinc-700">
                        <HeadingSmall :title="trans('setting.site_info.title')" :description="trans('setting.site_info.description')" />
                    </div>

                    <form @submit.prevent="submit" class="p-6">
                        <div class="space-y-8">
                            <!-- Basic Information Section -->
                            <div class="space-y-6">
                                <div class="mb-4 flex items-center gap-2 text-sm font-medium text-foreground">
                                    <Globe class="h-4 w-4" />
                                    {{ trans('setting.site_info.basic_information') }}
                                </div>
                                <div class="space-y-2">
                                    <Label for="site_name" class="text-sm font-medium text-muted-foreground">
                                        {{ trans('setting.site_info.site_name') }}
                                    </Label>
                                    <Input
                                        id="site_name"
                                        v-model="form.site_name"
                                        type="text"
                                        class="w-full"
                                        :placeholder="trans('setting.site_info.site_name_placeholder')"
                                    />
                                    <InputError :message="form.errors.site_name" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="meta_description" class="text-sm font-medium text-muted-foreground">
                                        {{ trans('setting.site_info.meta_description') }}
                                    </Label>
                                    <Textarea
                                        id="meta_description"
                                        v-model="form.meta_description"
                                        type="text"
                                        class="w-full"
                                        :placeholder="trans('setting.site_info.meta_description_placeholder')"
                                    />
                                    <InputError :message="form.errors.meta_description" />
                                </div>
                            </div>

                            <!-- Visual Assets Section -->
                            <div class="space-y-6">
                                <div class="mb-4 flex items-center gap-2 text-sm font-medium text-foreground">
                                    <Image class="h-4 w-4" />
                                    {{ trans('setting.site_info.visual_assets') }}
                                </div>

                                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                                    <div class="space-y-3">
                                        <Label for="logo_url" class="text-sm font-medium text-muted-foreground">
                                            {{ trans('setting.site_info.site_logo') }}
                                        </Label>
                                        <div
                                            class="flex items-center justify-center rounded-lg border border-border bg-muted p-4 dark:border-zinc-700 dark:bg-muted/30"
                                        >
                                            <UploadImage v-model="form.logo_url" />
                                        </div>
                                        <InputError :message="form.errors.logo_url" />
                                        <p class="text-xs text-muted-foreground">
                                            {{ trans('setting.site_info.site_logo_help') }}
                                        </p>
                                    </div>

                                    <div class="space-y-3">
                                        <Label for="favicon_url" class="text-sm font-medium text-muted-foreground">
                                            {{ trans('setting.site_info.favicon') }}
                                        </Label>
                                        <div
                                            class="flex items-center justify-center rounded-lg border border-border bg-muted p-4 dark:border-zinc-700 dark:bg-muted/30"
                                        >
                                            <UploadImage v-model="form.favicon_url" />
                                        </div>
                                        <InputError :message="form.errors.favicon_url" />
                                        <p class="text-xs text-muted-foreground">
                                            {{ trans('setting.site_info.favicon_help') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Prefixes Section -->
                            <div v-if="Object.keys(form.prefix).length > 0" class="space-y-6">
                                <div class="mb-4 flex items-center gap-2 text-sm font-medium text-foreground">
                                    <Hash class="h-4 w-4" />
                                    {{ trans('setting.site_info.url_prefixes') }}
                                </div>

                                <div class="rounded-lg border border-border bg-muted p-4 dark:border-zinc-700 dark:bg-muted/30">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                        <div v-for="key in Object.keys(form.prefix)" :key="key" class="space-y-2">
                                            <Label :for="`prefix-${key}`" class="text-sm font-medium text-muted-foreground">
                                                {{ trans(`setting.site_info.prefix_labels.${key}`) }}
                                            </Label>
                                            <Input
                                                :id="`prefix-${key}`"
                                                v-model="form.prefix[key]"
                                                type="text"
                                                class="w-full"
                                                :placeholder="trans('setting.site_info.prefix_placeholder', { key })"
                                            />
                                            <InputError :message="form.errors[`prefix.${key}`]" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 flex justify-end border-t border-border pt-8 dark:border-zinc-700">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-primary px-8 py-2 text-primary-foreground hover:bg-primary/90"
                            >
                                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                <FileText v-else class="mr-2 h-4 w-4" />
                                {{ trans('setting.site_info.save_changes') }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
