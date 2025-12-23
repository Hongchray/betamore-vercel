//Users/apple/Documents/Focuz Solution/betamore-admin/resources/js/pages/Customer/data/data.ts
import { ActionType } from '@/enums/action_menu';
import { Gender } from '@/enums/gender';
import { ToolbarAction } from '@/interfaces/ITable';
import { router } from '@inertiajs/vue3';
import { PhChatCircleSlash, PhGenderFemale, PhGenderMale } from '@phosphor-icons/vue';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import { route } from 'ziggy-js';
import { onDelete } from './events';

export const menuActions = computed(() => [
    {
        value: 'edit', // Just use a simple identifier
        type: ActionType.Event, // Change to Event type
        label: trans('customer.button.edit'),
        onClick: (data?: any) => {
            if (data && data.id) {
                router.visit(route('customers.edit', { customer: data.id }));
            }
        },
    },
    {
        value: 'edit',
        type: ActionType.Event,
        label: trans('customer.button.show'),
        onClick: (data?: any) => {
            if (data && data.id) {
                router.visit(route('customers.show', { customer: data.id }));
            }
        },
    },
    {
        value: '',
        type: ActionType.Event,
        label: trans('customer.button.delete'),
        onClick: (data?: any) => onDelete(data),
    },
]);

export const gender_options = computed(() => [
    {
        value: Gender.MALE,
        label: 'Male',
        icon: PhGenderFemale,
    },
    {
        value: Gender.FEMALE,
        label: 'Female',
        icon: PhGenderMale,
    },
    {
        value: Gender.OTHER,
        label: 'Others',
        icon: PhChatCircleSlash,
    },
]);

export const toolbarActions = computed((): ToolbarAction[] => [
    // {
    //     value: 'customers.create',
    //     type: ActionType.Link,
    //     label: trans('user.button.add_new'),
    //     icon: Plus,
    //     onClick: (data?: any) => void {},
    // },
]);

export function getCustomerBreadcrumbs() {
    // Changed function name
    return {
        index: computed(() => [
            {
                title: trans('user.breadcrumb.index'),
                href: '/customers',
            },
        ]),

        show: (customerId: string | number) =>
            computed(() => [
                {
                    title: trans('user.breadcrumb.index'),
                    href: '/customers',
                },
                {
                    title: trans('user.breadcrumb.show'),
                    href: `/customers/${customerId}`,
                },
            ]),

        create: computed(() => [
            {
                title: trans('user.breadcrumb.index'),
                href: '/customers',
            },
            {
                title: trans('user.breadcrumb.create'),
                href: '/customers/create',
            },
        ]),

        edit: (customerId: string | number) =>
            computed(() => [
                {
                    title: trans('user.breadcrumb.index'),
                    href: '/customers',
                },
                {
                    title: trans('user.breadcrumb.edit'),
                    href: `/customers/${customerId}/edit`,
                },
            ]),
    };
}
