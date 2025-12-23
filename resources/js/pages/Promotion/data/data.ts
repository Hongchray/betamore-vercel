import { ActionType } from '@/enums/action_menu';
import { ToolbarAction } from '@/interfaces/ITable';
import { router } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { Plus } from 'lucide-vue-next';
import { computed } from 'vue';
import { route } from 'ziggy-js';
import { onDelete } from './events';

export const menuActions = computed(() => [
    {
        value: 'edit',
        type: ActionType.Event,
        label: trans('promotion.button.edit'),
        onClick: (data?: any) => {
            if (data && data.id) {
                router.visit(route('promotions.edit', { promotion: data.id }));
            }
        },
    },
    {
        value: 'delete',
        type: ActionType.Event,
        label: trans('promotion.button.delete'),
        onClick: (data?: any) => onDelete(data),
    },
]);

export const toolbarActions = computed((): ToolbarAction[] => [
    {
        value: 'promotions.create',
        type: ActionType.Link,
        label: trans('promotion.button.add_new'),
        icon: Plus,
        onClick: (data?: any) => void {},
    },
]);

export function getPromotionBreadcrumbs() {
    return {
        index: computed(() => [
            {
                title: trans('promotion.breadcrumb.index'),
                href: '/promotions',
            },
        ]),

        show: (promotionId: string | number) =>
            computed(() => [
                {
                    title: trans('promotion.breadcrumb.index'),
                    href: '/promotions',
                },
                {
                    title: trans('promotion.breadcrumb.show'),
                    href: `/promotions/${promotionId}`,
                },
            ]),

        create: computed(() => [
            {
                title: trans('promotion.breadcrumb.index'),
                href: '/promotions',
            },
            {
                title: trans('promotion.breadcrumb.create'),
                href: '/promotions/create',
            },
        ]),

        edit: (promotionId: string | number) =>
            computed(() => [
                {
                    title: trans('promotion.breadcrumb.index'),
                    href: '/promotions',
                },
                {
                    title: trans('promotion.breadcrumb.edit'),
                    href: `/promotions/${promotionId}/edit`,
                },
            ]),
    };
}
