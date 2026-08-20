import { BadgeVariant } from '@/components/elements/Badge';

export const statusVariant = (status: string | null): BadgeVariant => {
    switch ((status || '').toLowerCase()) {
        case 'running':
            return 'success';
        case 'stopped':
        case 'install_failed':
            return 'danger';
        case 'installing':
            return 'warning';
        case 'suspended':
        case 'frozen':
            return 'info';
        default:
            return 'default';
    }
};
