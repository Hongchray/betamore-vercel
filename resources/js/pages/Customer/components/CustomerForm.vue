<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useToast } from '@/components/ui/toast/use-toast';
import CancelButton from '@/composables/ui/cancel-button/CancelButton.vue';
import Combobox from '@/composables/ui/combobox/Combobox.vue';
import PasswordField from '@/composables/ui/password/PasswordField.vue';
import RequiredMark from '@/composables/ui/required-mark/RequiredMark.vue';
import ImageUpload from '@/composables/ui/upload-image/UploadImage.vue';
import { UserType } from '@/enums/user_type';
import { useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { Loader2 } from 'lucide-vue-next';
import { computed } from 'vue';
import type { User } from '../data/schema';
type FormData = Omit<User, 'id'>;

interface ComboboxItem {
    value: string;
    label: string;
}

const props = defineProps<{
    user?: User | null;
    user_roles: ComboboxItem[];
}>();

const isEditMode = computed(() => props.user !== null && props.user !== undefined);
const submitButtonText = computed(() => (isEditMode.value ? trans('customer.button.update') : trans('customer.button.create')));
const { toast } = useToast();

const form = useForm<Partial<FormData>>({
    role_id: props.user?.role_id ?? props.user?.roles?.[0]?.id ?? undefined,
    first_name: props.user?.first_name ?? '',
    last_name: props.user?.last_name ?? '',
    gender: props.user?.gender ?? 'male',
    image: props.user?.image ?? '',
    email: props.user?.email ?? '',
    phone: props.user?.phone?.replace(/\s+/g, '') ?? '',
    telegram: props.user?.telegram ?? '',
    type: UserType.CUSTOMER,
    password: '',
    password_confirmation: '',
    roles: props.user?.roles ?? [],
});
const formattedPhone = computed({
    get: () => {
        // Group every 3 digits for display: 098888777 → 098 888 777
        return form.phone?.replace(/\s+/g, '').replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3') || '';
    },
    set: (value: string) => {
        // Strip spaces when setting back to form data
        form.phone = value.replace(/\s+/g, '');
    },
});
const gender_item = computed((): ComboboxItem[] => [
    { value: 'male', label: trans('customer.form.gender.male') },
    { value: 'female', label: trans('customer.form.gender.female') },
    { value: 'other', label: trans('customer.form.gender.other') },
]);

const handleSubmit = () => {
    const url = isEditMode.value ? route('customers.update', props.user!.id) : route('customers.store');
    const method = isEditMode.value ? 'put' : 'post';

    form[method](url, {
        onSuccess: () => {
            toast({
                variant: 'default',
                duration: 3000,
                description: isEditMode.value
                    ? trans('customer.messages.user_updated_successfully')
                    : trans('customer.messages.user_created_successfully'),
            });
        },
        onError: (error) => {
            toast({
                variant: 'destructive',
                duration: 3000,
                description: error?.message || trans('customer.messages.user_create_failed'),
            });
        },
    });
};
</script>

<template>
    <Card class="w-full rounded-xl border p-6 shadow-sm">
        <CardHeader class="mb-4">
            <CardTitle class="text-xl font-semibold">{{
                isEditMode ? trans('customer.form.edit_description') : trans('user.form.create_description')
            }}</CardTitle>
            <CardDescription class="text-sm text-muted-foreground"> </CardDescription>
        </CardHeader>
        <CardContent>
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <ImageUpload v-model="form.image" />

                <div class="grid grid-cols-2 gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="first_name">{{ trans('customer.form.fields.first_name') }} <RequiredMark /></Label>
                        <Input
                            id="first_name"
                            v-model="form.first_name"
                            type="text"
                            :placeholder="trans('customer.form.placeholders.first_name')"
                            :class="{ 'border-red-500': form.errors.first_name }"
                        />
                        <p v-if="form.errors.first_name" class="text-xs text-red-500">{{ form.errors.first_name }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label for="last_name">{{ trans('customer.form.fields.last_name') }} <RequiredMark /></Label>
                        <Input
                            id="last_name"
                            v-model="form.last_name"
                            type="text"
                            :placeholder="trans('customer.form.placeholders.last_name')"
                            :class="{ 'border-red-500': form.errors.last_name }"
                        />
                        <p v-if="form.errors.last_name" class="text-xs text-red-500">{{ form.errors.last_name }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="gender">{{ trans('customer.form.fields.gender') }} </Label>
                        <Combobox v-model="form.gender" :items="gender_item" emptyText="No gender found." />
                        <p v-if="form.errors.gender" class="text-xs text-red-500">{{ form.errors.gender }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label for="phone">{{ trans('customer.form.fields.phone') }}<RequiredMark /></Label>
                        <Input
                            id="phone"
                            v-model="formattedPhone"
                            type="text"
                            :placeholder="trans('customer.form.placeholders.phone')"
                            :class="{ 'border-red-500': form.errors.phone }"
                        />
                        <p v-if="form.errors.phone" class="text-xs text-red-500">{{ form.errors.phone }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="telegram">{{ trans('customer.form.fields.telegram') }}</Label>
                        <Input
                            id="telegram"
                            v-model="form.telegram"
                            type="text"
                            :placeholder="trans('customer.form.placeholders.telegram')"
                            :class="{ 'border-red-500': form.errors.telegram }"
                        />
                        <p v-if="form.errors.telegram" class="text-xs text-red-500">{{ form.errors.telegram }}</p>
                    </div>
                    <div class="space-y-2 md:col-span-1">
                        <Label for="email">{{ trans('customer.form.fields.email') }}</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            :placeholder="trans('customer.form.placeholders.email')"
                            :class="{ 'border-red-500': form.errors.email }"
                        />
                        <p v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6 md:grid-cols-2">
                    <PasswordField
                        id="password"
                        v-model="form.password"
                        :label="trans('customer.form.fields.password')"
                        :help-text="isEditMode ? trans('customer.form.password_help') : ''"
                        :placeholder="trans('customer.form.fields.password')"
                        :error="form.errors.password"
                    />

                    <PasswordField
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        :label="trans('customer.form.fields.password_confirmation')"
                        :placeholder="trans('customer.form.placeholders.password_confirmation')"
                        :error="form.errors.password_confirmation"
                    />
                </div>

                <div class="flex items-center justify-end gap-4">
                    <CancelButton :fallback-route="route('customers.index')" />
                    <Button type="submit" :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        {{ submitButtonText }}
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
