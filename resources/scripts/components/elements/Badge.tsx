import React from 'react';
import styled, { css } from 'styled-components/macro';
import tw from 'twin.macro';

export type BadgeVariant = 'default' | 'brand' | 'success' | 'warning' | 'danger' | 'info';

interface Props {
    variant?: BadgeVariant;
    className?: string;
    children: React.ReactNode;
}

const Wrapper = styled.span<{ $variant: BadgeVariant }>`
    ${tw`inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-2xs font-semibold uppercase tracking-wide leading-none whitespace-nowrap`};

    ${(props) => {
        switch (props.$variant) {
            case 'brand':
                return css`
                    background-color: rgb(var(--hyper-brand) / 0.16);
                    color: rgb(var(--hyper-brand-400));
                `;
            case 'success':
                return css`
                    background-color: rgb(var(--hyper-success) / 0.16);
                    color: rgb(var(--hyper-success));
                `;
            case 'warning':
                return css`
                    background-color: rgb(var(--hyper-warning) / 0.16);
                    color: rgb(var(--hyper-warning));
                `;
            case 'danger':
                return css`
                    background-color: rgb(var(--hyper-danger) / 0.16);
                    color: rgb(var(--hyper-danger));
                `;
            case 'info':
                return css`
                    background-color: rgb(var(--hyper-info) / 0.16);
                    color: rgb(var(--hyper-info));
                `;
            default:
                return css`
                    background-color: rgb(var(--hyper-surface-3));
                    color: rgb(var(--hyper-text-muted));
                `;
        }
    }}
`;

const Badge = ({ variant = 'default', className, children }: Props) => (
    <Wrapper $variant={variant} className={className}>
        {children}
    </Wrapper>
);

export default Badge;
