<script setup lang="ts">
import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useToast } from '@/components/ui/toast/use-toast';
import Combobox from '@/composables/ui/combobox/Combobox.vue';
import UploadImage from '@/composables/ui/upload-image/UploadImage.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { User } from '@/pages/User/data/schema';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { Loader2 } from 'lucide-vue-next';
import { computed } from 'vue';

interface ComboboxItem {
    value: string;
    label: string;
}

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbs = computed((): BreadcrumbItem[] => [
    {
        title: trans('setting.breadcrumb.profile'),
        href: '/settings/profile',
    },
]);

const page = usePage();
const user = page.props.auth.user as unknown as User;
const { toast } = useToast();

const form = useForm({
    first_name: user.first_name,
    last_name: user.last_name,
    email: user.email,
    phone: user.phone,
    image: user.image,
    gender: user.gender,
    telegram: user.telegram,
});

const gender_item = computed((): ComboboxItem[] => [
    { value: 'male', label: trans('customer.form.gender.male') },
    { value: 'female', label: trans('customer.form.gender.female') },
    { value: 'other', label: trans('customer.form.gender.other') },
]);
const formattedPhone = computed({
    get: () => {
        return form.phone?.replace(/\s+/g, '').replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3') || '';
    },
    set: (value: string) => {
        // Strip spaces when setting back to form data
        form.phone = value.replace(/\s+/g, '');
    },
});
const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            toast({
                variant: 'default',
                duration: 3000,
                description: trans('setting.profile.messages.profile_updated_successfully'),
            });
        },
        onError: (error) => {
            toast({
                variant: 'destructive',
                duration: 3000,
                description: error?.message || trans('setting.profile.messages.profile_update_failed'),
            });
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile Setting" />

        <SettingsLayout class="max-w-5xl">
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    :title="trans('setting.profile.profile_information_title')"
                    :description="trans('setting.profile.profile_information_description')"
                />
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="image">{{ trans('setting.profile.image') }}</Label>
                        <UploadImage v-model="form.image" />

                        <InputError class="mt-2" :message="form.errors.image" />
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="first_name">{{ trans('setting.profile.first_name') }}</Label>
                            <Input
                                id="first_name"
                                class="mt-1 block w-full"
                                v-model="form.first_name"
                                autocomplete="name"
                                :placeholder="trans('setting.profile.first_name')"
                            />
                            <InputError class="mt-2" :message="form.errors.first_name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="last_name">{{ trans('setting.profile.last_name') }}</Label>
                            <Input
                                id="last_name"
                                class="mt-1 block w-full"
                                v-model="form.last_name"
                                autocomplete="last_name"
                                :placeholder="trans('setting.profile.last_name')"
                            />
                            <InputError class="mt-2" :message="form.errors.last_name" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="email">{{ trans('setting.profile.email_address') }}</Label>
                            <Input
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                autocomplete="username"
                                :placeholder="trans('setting.profile.email_placeholder')"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="phone">{{ trans('setting.profile.phone') }}</Label>
                            <Input
                                id="phone"
                                type="tel"
                                class="mt-1 block w-full"
                                v-model="formattedPhone"
                                autocomplete="tel"
                                :placeholder="trans('setting.profile.phone')"
                            />
                            <InputError class="mt-2" :message="form.errors.phone" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="gender">{{ trans('setting.profile.gender') }}</Label>
                            <Combobox v-model="form.gender" :items="gender_item" emptyText="No gender found." />

                            <InputError class="mt-2" :message="form.errors.gender" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="telegram">{{ trans('setting.profile.telegram') }}</Label>
                            <Input
                                id="telegram"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.telegram"
                                :placeholder="trans('setting.profile.telegram')"
                            />
                            <InputError class="mt-2" :message="form.errors.telegram" />
                        </div>
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            {{ trans('setting.profile.email_unverified') }}
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                {{ trans('setting.profile.resend_verification') }}
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            {{ trans('setting.profile.verification_link_sent') }}
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">
                            <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                            {{ trans('setting.profile.save') }}
                        </Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">{{ trans('setting.profile.saved') }}</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
