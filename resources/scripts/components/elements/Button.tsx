import React from 'react';
import styled, { css } from 'styled-components/macro';
import tw from 'twin.macro';
import Spinner from '@/components/elements/Spinner';

interface Props {
    isLoading?: boolean;
    size?: 'xsmall' | 'small' | 'large' | 'xlarge';
    color?: 'green' | 'red' | 'primary' | 'grey';
    isSecondary?: boolean;
}

const ButtonStyle = styled.button<Omit<Props, 'isLoading'>>`
    ${tw`relative inline-flex items-center justify-center gap-2 rounded-xl font-medium text-sm transition-all duration-150 border select-none`};
    letter-spacing: 0.01em;

    &:focus-visible {
        outline: 2px solid rgb(var(--hyper-brand) / 0.6);
        outline-offset: 2px;
    }

    ${(props) =>
        ((!props.isSecondary && !props.color) || props.color === 'primary') &&
        css<Props>`
            ${(props) =>
                !props.isSecondary &&
                css`
                    background-image: linear-gradient(135deg, rgb(var(--hyper-brand)) 0%, rgb(var(--hyper-brand-600)) 100%);
                    border-color: rgb(var(--hyper-brand-700));
                    color: white;
                    box-shadow: 0 1px 0 0 rgb(255 255 255 / 0.08) inset, 0 8px 20px -8px rgb(var(--hyper-brand) / 0.55);
                `};

            &:hover:not(:disabled) {
                filter: brightness(1.07);
                transform: translateY(-1px);
            }

            &:active:not(:disabled) {
                transform: translateY(0);
                filter: brightness(0.96);
            }
        `};

    ${(props) =>
        props.color === 'grey' &&
        css`
            background-color: rgb(var(--hyper-surface-3));
            border-color: rgb(var(--hyper-border-strong));
            color: rgb(var(--hyper-text));

            &:hover:not(:disabled) {
                background-color: rgb(var(--hyper-surface-2));
            }
        `};

    ${(props) =>
        props.color === 'green' &&
        css<Props>`
            background-color: rgb(var(--hyper-success));
            border-color: rgb(var(--hyper-success));
            color: white;

            &:hover:not(:disabled) {
                filter: brightness(1.08);
            }
        `};

    ${(props) =>
        props.color === 'red' &&
        css<Props>`
            background-color: rgb(var(--hyper-danger));
            border-color: rgb(var(--hyper-danger));
            color: white;

            &:hover:not(:disabled) {
                filter: brightness(1.08);
            }
        `};

    ${(props) => props.size === 'xsmall' && tw`px-2.5 py-1 text-xs`};
    ${(props) => (!props.size || props.size === 'small') && tw`px-4 py-2`};
    ${(props) => props.size === 'large' && tw`px-5 py-2.5 text-sm`};
    ${(props) => props.size === 'xlarge' && tw`px-4 py-3 w-full`};

    ${(props) =>
        props.isSecondary &&
        css<Props>`
            background-color: transparent;
            border-color: rgb(var(--hyper-border-strong));
            color: rgb(var(--hyper-text-muted));

            &:hover:not(:disabled) {
                border-color: rgb(var(--hyper-brand) / 0.5);
                color: rgb(var(--hyper-text));
                background-color: rgb(var(--hyper-brand) / 0.08);

                ${(props) => props.color === 'red' && tw`border-transparent`};
                ${(props) =>
                    props.color === 'red' &&
                    css`
                        background-color: rgb(var(--hyper-danger));
                        border-color: rgb(var(--hyper-danger));
                        color: white;
                    `};
                ${(props) =>
                    props.color === 'green' &&
                    css`
                        background-color: rgb(var(--hyper-success));
                        border-color: rgb(var(--hyper-success));
                        color: white;
                    `};
            }
        `};

    &:disabled {
        opacity: 0.5;
        cursor: default;
        transform: none !important;
    }
`;

type ComponentProps = Omit<JSX.IntrinsicElements['button'], 'ref' | keyof Props> & Props;

const Button: React.FC<ComponentProps> = ({ children, isLoading, ...props }) => (
    <ButtonStyle {...props}>
        {isLoading && (
            <div css={tw`flex absolute justify-center items-center w-full h-full left-0 top-0`}>
                <Spinner size={'small'} />
            </div>
        )}
        <span css={isLoading ? tw`text-transparent` : tw`inline-flex items-center gap-2`}>{children}</span>
    </ButtonStyle>
);

type LinkProps = Omit<JSX.IntrinsicElements['a'], 'ref' | keyof Props> & Props;

const LinkButton: React.FC<LinkProps> = (props) => <ButtonStyle as={'a'} {...props} />;

export { LinkButton, ButtonStyle };
export default Button;
