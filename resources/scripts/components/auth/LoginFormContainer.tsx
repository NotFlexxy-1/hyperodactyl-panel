import React, { forwardRef } from 'react';
import { Form } from 'formik';
import styled from 'styled-components/macro';
import { breakpoint } from '@/theme';
import FlashMessageRender from '@/components/FlashMessageRender';
import tw from 'twin.macro';

type Props = React.DetailedHTMLProps<React.FormHTMLAttributes<HTMLFormElement>, HTMLFormElement> & {
    title?: string;
};

const Container = styled.div`
    ${tw`w-full`};

    ${breakpoint('sm')`
        ${tw`w-4/5 mx-auto`}
    `};

    ${breakpoint('lg')`
        ${tw`w-full max-w-[420px] mx-0`}
    `};
`;

const Page = styled.div`
    ${tw`min-h-screen w-full flex`};
    background-color: rgb(var(--hyper-surface-0));
`;

const SidePanel = styled.div`
    ${tw`hidden lg:flex flex-col justify-between w-1/2 p-16 relative overflow-hidden`};
    background-image: linear-gradient(160deg, rgb(var(--hyper-brand)) 0%, rgb(var(--hyper-accent)) 100%);

    &::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle at 1px 1px, rgb(255 255 255 / 0.16) 1px, transparent 0);
        background-size: 22px 22px;
        opacity: 0.5;
    }
`;

export default forwardRef<HTMLFormElement, Props>(({ title, ...props }, ref) => (
    <Page>
        <SidePanel>
            <img src={'/assets/svgs/hyperodactyl.svg'} css={tw`w-40 relative z-10`} />
            <div css={tw`relative z-10`}>
                <h2 css={tw`text-white text-4xl font-header font-bold max-w-md leading-tight`}>
                    Manage your infrastructure with confidence.
                </h2>
                <p css={tw`text-white/80 mt-4 max-w-sm`}>
                    A modern, premium control panel for your servers — fast, secure, and built for scale.
                </p>
            </div>
            <p css={tw`text-white/60 text-xs relative z-10`}>&copy; 2015 - {new Date().getFullYear()} Hyperodactyl</p>
        </SidePanel>
        <div css={tw`flex-1 flex items-center justify-center p-6 sm:p-10`}>
            <Container className={'animate-fade-in-up'}>
                {title && (
                    <h2 css={tw`text-2xl font-header font-semibold py-4`} style={{ color: 'rgb(var(--hyper-text))' }}>
                        {title}
                    </h2>
                )}
                <FlashMessageRender css={tw`mb-4`} />
                <Form {...props} ref={ref}>
                    <div
                        css={tw`w-full rounded-2xl p-6 sm:p-8 border`}
                        style={{
                            backgroundColor: 'rgb(var(--hyper-surface-2) / 0.9)',
                            borderColor: 'rgb(var(--hyper-border))',
                            backdropFilter: 'blur(10px)',
                            boxShadow: '0 1px 1px rgb(0 0 0 / 0.2), 0 20px 48px -18px rgb(0 0 0 / 0.6)',
                        }}
                    >
                        <div className={'lg:hidden mb-6 flex justify-center'}>
                            <img src={'/assets/svgs/hyperodactyl.svg'} css={tw`w-32`} />
                        </div>
                        {props.children}
                    </div>
                </Form>
                <p css={tw`text-center text-xs mt-6`} style={{ color: 'rgb(var(--hyper-text-subtle))' }}>
                    &copy; 2015 - {new Date().getFullYear()}&nbsp;
                    <a
                        rel={'noopener nofollow noreferrer'}
                        href={'https://hyperodactyl.io'}
                        target={'_blank'}
                        css={tw`no-underline hover:underline`}
                        style={{ color: 'rgb(var(--hyper-text-subtle))' }}
                    >
                        Hyperodactyl Software
                    </a>
                </p>
            </Container>
        </div>
    </Page>
));
