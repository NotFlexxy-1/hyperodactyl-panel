import React from 'react';
import tw from 'twin.macro';

interface Props {
    title: React.ReactNode;
    description?: React.ReactNode;
    actions?: React.ReactNode;
    className?: string;
}

const PageHeader = ({ title, description, actions, className }: Props) => (
    <div css={tw`flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-fade-in-up`} className={className}>
        <div>
            <h1 css={tw`text-2xl sm:text-3xl font-header font-semibold tracking-tight`} style={{ color: 'rgb(var(--hyper-text))' }}>
                {title}
            </h1>
            {description && (
                <p css={tw`text-sm mt-1`} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                    {description}
                </p>
            )}
        </div>
        {actions && <div css={tw`flex items-center gap-2 flex-wrap`}>{actions}</div>}
    </div>
);

export default PageHeader;
