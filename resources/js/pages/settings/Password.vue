<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { useToast } from '@/components/ui/toast/use-toast';
import PasswordField from '@/composables/ui/password/PasswordField.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed, ref } from 'vue';
const breadcrumbItems = computed((): BreadcrumbItem[] => [
    {
        title: trans('setting.breadcrumb.password'),
        href: '/settings/password',
    },
]);

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);
const { toast } = useToast();

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset(),
                toast({
                    variant: 'default',
                    duration: 3000,
                    description: trans('setting.password.saved'),
                });
        },
        onError: (errors: any) => {
            toast({
                variant: 'destructive',
                duration: 3000,
                description: errors?.message || trans('setting.password.error_message'),
            });
            if (errors.password) {
                form.reset('password', 'password_confirmation');
                if (passwordInput.value instanceof HTMLInputElement) {
                    passwordInput.value.focus();
                }
            }

            if (errors.current_password) {
                form.reset('current_password');
                if (currentPasswordInput.value instanceof HTMLInputElement) {
                    currentPasswordInput.value.focus();
                }
            }
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Password settings" />

        <SettingsLayout class="max-w-4xl">
            <div class="space-y-6">
                <HeadingSmall :title="trans('setting.password.title')" :description="trans('setting.password.description')" />

                <form @submit.prevent="updatePassword" class="space-y-6">
                    <div class="grid gap-2">
                        <PasswordField
                            id="current_password"
                            ref="currentPasswordInput"
                            :label="trans('setting.password.current_password_placeholder')"
                            v-model="form.current_password"
                            class="mt-1 block w-full"
                            :placeholder="trans('setting.password.current_password_placeholder')"
                        />
                        <InputError :message="form.errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <PasswordField
                            id="password"
                            ref="passwordInput"
                            :label="trans('setting.password.new_password_placeholder')"
                            v-model="form.password"
                            class="mt-1 block w-full"
                            :placeholder="trans('setting.password.new_password_placeholder')"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <PasswordField
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            class="mt-1 block w-full"
                            :label="trans('setting.password.confirm_password_placeholder')"
                            :placeholder="trans('setting.password.confirm_password_placeholder')"
                        />
                        <InputError :message="form.errors.password_confirmation" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">{{ trans('setting.password.save_password') }}</Button>

                        <!-- <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">{{ trans('setting.password.saved') }}</p>
                        </Transition> -->
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
