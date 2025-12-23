import { computed, ref } from 'vue';

export interface PasswordFieldOptions {
    required?: boolean;
    placeholder?: string;
    label?: string;
    helpText?: string;
}

export function usePasswordField(options: PasswordFieldOptions = {}) {
    const showPassword = ref(false);

    const defaultOptions: PasswordFieldOptions = {
        required: true,
        placeholder: 'Enter password',
        label: 'Password',
        ...options,
    };

    const togglePasswordVisibility = () => {
        showPassword.value = !showPassword.value;
    };

    const inputType = computed(() => (showPassword.value ? 'text' : 'password'));

    return {
        showPassword,
        togglePasswordVisibility,
        inputType,
        options: defaultOptions,
    };
}
