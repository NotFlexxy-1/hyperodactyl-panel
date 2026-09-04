import React from 'react';
import tw from 'twin.macro';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconProp } from '@fortawesome/fontawesome-svg-core';
import { faBoxOpen } from '@fortawesome/free-solid-svg-icons';

interface Props {
    icon?: IconProp;
    title: string;
    description?: React.ReactNode;
    action?: React.ReactNode;
    className?: string;
}

const EmptyState = ({ icon = faBoxOpen, title, description, action, className }: Props) => (
    <div css={tw`flex flex-col items-center justify-center text-center py-12 px-6 animate-fade-in`} className={className}>
        <div
            css={tw`w-14 h-14 rounded-2xl flex items-center justify-center mb-4`}
            style={{ backgroundColor: 'rgb(var(--hyper-surface-3))' }}
        >
            <FontAwesomeIcon icon={icon} css={tw`text-xl`} style={{ color: 'rgb(var(--hyper-text-subtle))' }} />
        </div>
        <p css={tw`font-semibold text-base`} style={{ color: 'rgb(var(--hyper-text))' }}>
            {title}
        </p>
        {description && (
            <p css={tw`text-sm mt-1 max-w-md`} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                {description}
            </p>
        )}
        {action && <div css={tw`mt-5`}>{action}</div>}
    </div>
);

export default EmptyState;
