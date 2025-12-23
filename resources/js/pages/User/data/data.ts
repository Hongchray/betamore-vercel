import { ActionType } from '@/enums/action_menu';
import { Gender } from '@/enums/gender';
import { ToolbarAction } from '@/interfaces/ITable';
import { PhChatCircleSlash, PhGenderFemale, PhGenderMale } from '@phosphor-icons/vue';
import { trans } from 'laravel-vue-i18n';
import { Plus } from 'lucide-vue-next';
import { computed } from 'vue';
import { onDelete } from './events';

export const menuActions = computed(() => [
    {
        value: {
            name: 'users.edit',
            params: {
                id: null,
            },
        },
        type: ActionType.Link,
        label: trans('user.button.edit'),
    },
    {
        value: '',
        type: ActionType.Event,
        label: trans('user.button.delete'),
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
    {
        value: 'users.create',
        type: ActionType.Link,
        label: trans('user.button.add_new'),
        icon: Plus,
        onClick: (data?: any) => void {},
    },
]);

export function getAdminBreadcrumbs() {
    return {
        index: computed(() => [
            {
                title: trans('user.breadcrumb.index'),
                href: '/users',
            },
        ]),

        show: (userId: string | number) =>
            computed(() => [
                {
                    title: trans('user.breadcrumb.index'),
                    href: '/users',
                },
                {
                    title: trans('user.breadcrumb.show'),
                    href: `/users/${userId}`,
                },
            ]),

        create: computed(() => [
            {
                title: trans('user.breadcrumb.index'),
                href: '/users',
            },
            {
                title: trans('user.breadcrumb.create'),
                href: '/users/create',
            },
        ]),

        edit: (userId: string | number) =>
            computed(() => [
                {
                    title: trans('user.breadcrumb.index'),
                    href: '/users',
                },
                {
                    title: trans('user.breadcrumb.edit'),
                    href: `/users/${userId}/edit`,
                },
            ]),
    };
}
