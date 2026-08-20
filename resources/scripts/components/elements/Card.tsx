import React from 'react';
import styled, { css } from 'styled-components/macro';
import tw from 'twin.macro';

interface Props {
    $hoverable?: boolean;
    $padded?: boolean;
    as?: React.ElementType;
}

const Card = styled.div<Props>`
    ${tw`relative rounded-2xl border overflow-hidden`};
    background-color: rgb(var(--hyper-surface-2) / 0.85);
    border-color: rgb(var(--hyper-border));
    box-shadow: 0 1px 1px rgb(0 0 0 / 0.2), 0 12px 32px -14px rgb(0 0 0 / 0.55);
    backdrop-filter: blur(10px);

    ${(props) => props.$padded !== false && tw`p-4 sm:p-5`};

    ${(props) =>
        props.$hoverable &&
        css`
            transition:
                transform 200ms cubic-bezier(0.16, 1, 0.3, 1),
                border-color 200ms ease,
                box-shadow 200ms ease;

            &:hover {
                transform: translateY(-2px);
                border-color: rgb(var(--hyper-brand) / 0.45);
                box-shadow: 0 1px 1px rgb(0 0 0 / 0.2), 0 16px 40px -14px rgb(0 0 0 / 0.65);
            }
        `};
`;

export const CardHeader = styled.div`
    ${tw`flex items-center justify-between gap-3 mb-4`};
`;

export const CardTitle = styled.h3`
    ${tw`text-sm font-semibold uppercase tracking-wide`};
    color: rgb(var(--hyper-text-muted));
`;

export default Card;
