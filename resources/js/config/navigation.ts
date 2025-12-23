import { type NavItem, type NavSection } from '@/interfaces/SideBar';
import { trans } from 'laravel-vue-i18n';
import {
    FlagIcon,
    LayoutGrid,
    Newspaper,
    Package,
    PlaneTakeoff,
    Receipt,
    Settings,
    ShoppingBag,
    TagIcon,
    User,
    UserCog,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';

export const mainNavSections = computed((): NavSection[] => [
    {
        title: trans('navigation.dashboard'),
        items: [
            {
                title: trans('navigation.dashboard'),
                href: '/dashboard',
                icon: LayoutGrid,
            },
        ],
    },
    {
        title: trans('navigation.banner_management'),
        items: [
            {
                title: trans('navigation.banners'),
                href: '/banners',
                icon: FlagIcon,
            },
        ],
    },
    {
        title: trans('navigation.item_management'),
        items: [
            {
                title: trans('navigation.companies'),
                href: '/companies',
                icon: Newspaper,
            },
            {
                title: trans('navigation.items'),
                href: '/items',
                icon: Package,
            },
        ],
    },

    {
        title: trans('navigation.marketing_management'),
        items: [
            {
                title: trans('navigation.promotions'),
                href: '/promotions',
                icon: TagIcon,
            },
        ],
    },

    {
        title: trans('navigation.user_management'),
        items: [
            {
                title: trans('navigation.customers'),
                href: '/customers',
                icon: Users,
            },
        ],
    },

    {
        title: trans('navigation.delivery_management'),
        items: [
            {
                title: trans('navigation.delivery'),
                href: '/deliveries',
                icon: PlaneTakeoff,
            },
        ],
    },

    {
        title: trans('navigation.order_management'),
        items: [
            {
                title: trans('navigation.orders'),
                href: '/orders',
                icon: ShoppingBag,
            },
            {
                title: trans('navigation.payments'),
                href: '/payments',
                icon: Receipt,
            },
        ],
    },

    {
        title: trans('navigation.report_management'),
        items: [
            {
                title: trans('navigation.report'),
                href: '/reports',
                icon: Receipt,
            },
        ],
    },

    {
        title: trans('navigation.system'),
        items: [
            {
                title: trans('navigation.settings'),
                href: '/settings',
                icon: Settings,
            },
            {
                title: trans('navigation.users'),
                href: '/users',
                icon: User,
            },
            {
                title: trans('navigation.roles'),
                href: '/roles',
                icon: UserCog,
            },
        ],
    },
]);

export const footerNavItems = computed((): NavItem[] => [
    // {
    //     title: 'Github Repo',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#vue',
    //     icon: BookOpen,
    // },
]);
