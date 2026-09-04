import React from 'react';
import FlashMessageRender from '@/components/FlashMessageRender';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import tw from 'twin.macro';

type Props = Readonly<
    React.DetailedHTMLProps<React.HTMLAttributes<HTMLDivElement>, HTMLDivElement> & {
        title?: string;
        borderColor?: string;
        showFlashes?: string | boolean;
        showLoadingOverlay?: boolean;
    }
>;

const ContentBox = ({ title, borderColor, showFlashes, showLoadingOverlay, children, ...props }: Props) => (
    <div {...props}>
        {title && (
            <h2 css={tw`mb-4 px-1 text-xl font-header font-semibold tracking-tight`} style={{ color: 'rgb(var(--hyper-text))' }}>
                {title}
            </h2>
        )}
        {showFlashes && (
            <FlashMessageRender byKey={typeof showFlashes === 'string' ? showFlashes : undefined} css={tw`mb-4`} />
        )}
        <div
            css={[tw`rounded-2xl p-4 sm:p-5 relative border`, !!borderColor && tw`border-t-4`]}
            style={{
                backgroundColor: 'rgb(var(--hyper-surface-2) / 0.85)',
                borderColor: borderColor || 'rgb(var(--hyper-border))',
                backdropFilter: 'blur(10px)',
                boxShadow: '0 1px 1px rgb(0 0 0 / 0.2), 0 12px 32px -14px rgb(0 0 0 / 0.55)',
            }}
        >
            <SpinnerOverlay visible={showLoadingOverlay || false} />
            {children}
        </div>
    </div>
);

export default ContentBox;
