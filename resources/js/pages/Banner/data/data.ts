import { ActionType } from '@/enums/action_menu';
import { ToolbarAction } from '@/interfaces/ITable';
import { trans } from 'laravel-vue-i18n';
import { Plus } from 'lucide-vue-next';
import { computed } from 'vue';
import { onDelete } from './events';

export const menuActions = computed(() => [
    {
        value: {
            name: 'banners.edit',
            params: {
                id: null,
            },
        },
        type: ActionType.Link,
        label: trans('banner.button.edit'),
    },
    {
        value: '',
        type: ActionType.Event,
        label: trans('banner.button.delete'),
        onClick: (data?: any) => onDelete(data),
    },
]);

export const toolbarActions = computed((): ToolbarAction[] => [
    {
        value: 'banners.create',
        type: ActionType.Link,
        label: trans('banner.button.add_new'),
        icon: Plus,
        onClick: (data?: any) => void {},
    },
]);

export function getAdminBreadcrumbs() {
    return {
        index: computed(() => [
            {
                title: trans('banner.breadcrumb.index'),
                href: '/banners',
            },
        ]),

        show: (banner: string | number) =>
            computed(() => [
                {
                    title: trans('banner.breadcrumb.index'),
                    href: '/banners',
                },
                {
                    title: trans('banner.breadcrumb.show'),
                    href: `/banners/${banner}`,
                },
            ]),

        create: computed(() => [
            {
                title: trans('banner.breadcrumb.index'),
                href: '/banners',
            },
            {
                title: trans('banner.breadcrumb.create'),
                href: '/banners/create',
            },
        ]),

        edit: (banner: string | number) =>
            computed(() => [
                {
                    title: trans('banner.breadcrumb.index'),
                    href: '/banners',
                },
                {
                    title: trans('banner.breadcrumb.edit'),
                    href: `/banners/${banner}/edit`,
                },
            ]),
    };
}
