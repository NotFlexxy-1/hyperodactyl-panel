import React, { memo } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconProp } from '@fortawesome/fontawesome-svg-core';
import tw from 'twin.macro';
import isEqual from 'react-fast-compare';

interface Props {
    icon?: IconProp;
    title: string | React.ReactNode;
    className?: string;
    children: React.ReactNode;
}

const TitledGreyBox = ({ icon, title, children, className }: Props) => (
    <div
        css={tw`rounded-2xl overflow-hidden border`}
        style={{ backgroundColor: 'rgb(var(--hyper-surface-2) / 0.85)', borderColor: 'rgb(var(--hyper-border))' }}
        className={className}
    >
        <div
            css={tw`p-3 px-4 border-b`}
            style={{ backgroundColor: 'rgb(var(--hyper-surface-3) / 0.6)', borderColor: 'rgb(var(--hyper-border))' }}
        >
            {typeof title === 'string' ? (
                <p css={tw`text-xs uppercase tracking-wide font-semibold`} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                    {icon && <FontAwesomeIcon icon={icon} css={tw`mr-2`} />}
                    {title}
                </p>
            ) : (
                title
            )}
        </div>
        <div css={tw`p-4`}>{children}</div>
    </div>
);

export default memo(TitledGreyBox, isEqual);
