<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import PasswordField from '@/composables/ui/password/PasswordField.vue';
import RequiredMark from '@/composables/ui/required-mark/RequiredMark.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { LoaderCircle } from 'lucide-vue-next';
defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase :title="trans('auth.login.title')" :description="trans('auth.login.description')">
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">{{ trans('auth.login.email_label') }} <RequiredMark /></Label>
                    <Input
                        id="email"
                        type="email"
                        autofocus
                        required
                        :tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        :placeholder="trans('auth.login.email_placeholder')"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">{{ trans('auth.login.password_label') }} <RequiredMark /></Label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm" :tabindex="5">
                            {{ trans('auth.login.forgot_password') }}
                        </TextLink>
                    </div>
                    <PasswordField
                        id="password"
                        type="password"
                        required
                        :tabindex="2"
                        label=""
                        autocomplete="current-password"
                        v-model="form.password"
                        :placeholder="trans('auth.login.password_placeholder')"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model="form.remember" :tabindex="3" />
                        <span>{{ trans('auth.login.remember_me') }}</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    {{ trans('auth.login.login_button') }}
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                {{ trans('auth.designed_maintained_by') }}
                <TextLink target="_blank" :href="'https://www.facebook.com/FocuzSolution'" :tabindex="5">
                    {{ trans('auth.focuzsolution') }}
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>
