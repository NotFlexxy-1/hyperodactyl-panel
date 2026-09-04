import styled from 'styled-components/macro';
import tw from 'twin.macro';

const Label = styled.label<{ isLight?: boolean }>`
    ${tw`block text-xs uppercase tracking-wide mb-1 sm:mb-2`};
    color: rgb(var(--hyper-text-subtle));
    ${(props) =>
        props.isLight &&
        `
        color: rgb(var(--hyper-text));
        opacity: 0.85;
    `};
`;

export default Label;
