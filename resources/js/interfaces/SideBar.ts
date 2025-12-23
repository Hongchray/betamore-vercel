// Add this interface
export interface NavSection {
    title: string;
    items: NavItem[];
}

// Keep your existing NavItem interface
export interface NavItem {
    title: string;
    href: string;
    icon?: any;
    isActive?: boolean;
    items?: NavItem[];
}
