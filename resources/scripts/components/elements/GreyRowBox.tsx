import styled from 'styled-components/macro';
import tw from 'twin.macro';

export default styled.div<{ $hoverable?: boolean }>`
    ${tw`flex rounded-xl no-underline items-center p-4 border transition-all duration-150 overflow-hidden`};
    background-color: rgb(var(--hyper-surface-2) / 0.85);
    border-color: rgb(var(--hyper-border));
    color: rgb(var(--hyper-text));
    backdrop-filter: blur(8px);

    ${(props) =>
        props.$hoverable !== false &&
        `
        &:hover {
            border-color: rgb(var(--hyper-brand) / 0.4);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px -12px rgb(0 0 0 / 0.6);
        }
    `};

    & .icon {
        ${tw`rounded-xl w-12 h-12 flex items-center justify-center p-3 flex-shrink-0`};
        background-color: rgb(var(--hyper-brand) / 0.14);
        color: rgb(var(--hyper-brand-400));
    }
`;
