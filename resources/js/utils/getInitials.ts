//Get Initial form name
export const getInitials = (name: string) =>
    name
        .split(' ')
        .map((n) => n[0])
        .join('');
