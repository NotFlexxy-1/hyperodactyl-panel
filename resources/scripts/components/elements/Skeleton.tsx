import React from 'react';
import styled from 'styled-components/macro';
import tw from 'twin.macro';

interface Props {
    width?: string;
    height?: string;
    circle?: boolean;
    className?: string;
}

const Shimmer = styled.div<{ $circle?: boolean }>`
    ${tw`animate-shimmer`};
    ${(props) => (props.$circle ? tw`rounded-full` : tw`rounded-lg`)};
    background: linear-gradient(
        90deg,
        rgb(var(--hyper-surface-3)) 25%,
        rgb(var(--hyper-surface-2)) 37%,
        rgb(var(--hyper-surface-3)) 63%
    );
    background-size: 400% 100%;
`;

const Skeleton = ({ width = '100%', height = '1rem', circle, className }: Props) => (
    <Shimmer style={{ width, height }} $circle={circle} className={className} />
);

export const SkeletonRow = ({ count = 1 }: { count?: number }) => (
    <div css={tw`space-y-2 w-full`}>
        {Array.from({ length: count }).map((_, i) => (
            <Skeleton key={i} height={'2.5rem'} />
        ))}
    </div>
);

export default Skeleton;
