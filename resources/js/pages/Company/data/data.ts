import { ActionType } from '@/enums/action_menu';
import { ToolbarAction } from '@/interfaces/ITable';
import { trans } from 'laravel-vue-i18n';
import { Plus } from 'lucide-vue-next';
import { computed } from 'vue';
import { onDelete } from './events';

export const menuActions = computed(() => [
    {
        value: {
            name: 'companies.edit',
            params: {
                id: null,
            },
        },
        type: ActionType.Link,
        label: trans('company.button.edit'),
    },
    {
        value: '',
        type: ActionType.Event,
        label: trans('company.button.delete'),
        onClick: (data?: any) => onDelete(data),
    },
]);

export const toolbarActions = computed((): ToolbarAction[] => [
    {
        value: 'companies.create',
        type: ActionType.Link,
        label: trans('company.button.add_new'),
        icon: Plus,
        onClick: (data?: any) => void {},
    },
]);

export function getAdminBreadcrumbs() {
    return {
        index: computed(() => [
            {
                title: trans('company.breadcrumb.index'),
                href: '/companies',
            },
        ]),

        show: (company: string | number) =>
            computed(() => [
                {
                    title: trans('company.breadcrumb.index'),
                    href: '/companies',
                },
                {
                    title: trans('company.breadcrumb.show'),
                    href: `/companies/${company}`,
                },
            ]),

        create: computed(() => [
            {
                title: trans('company.breadcrumb.index'),
                href: '/companies',
            },
            {
                title: trans('company.breadcrumb.create'),
                href: '/companies/create',
            },
        ]),

        edit: (company: string | number) =>
            computed(() => [
                {
                    title: trans('company.breadcrumb.index'),
                    href: '/companies',
                },
                {
                    title: trans('company.breadcrumb.edit'),
                    href: `/companies/${company}/edit`,
                },
            ]),
    };
}
