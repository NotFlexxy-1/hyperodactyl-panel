import React from 'react';
import tw from 'twin.macro';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faExclamationTriangle } from '@fortawesome/free-solid-svg-icons';
import Button from '@/components/elements/Button';

interface Props {
    title?: string;
    message?: string;
    onRetry?: () => void;
    className?: string;
}

const ErrorState = ({ title = 'Something went wrong', message, onRetry, className }: Props) => (
    <div css={tw`flex flex-col items-center justify-center text-center py-12 px-6 animate-fade-in`} className={className}>
        <div
            css={tw`w-14 h-14 rounded-2xl flex items-center justify-center mb-4`}
            style={{ backgroundColor: 'rgb(var(--hyper-danger) / 0.14)' }}
        >
            <FontAwesomeIcon icon={faExclamationTriangle} css={tw`text-xl`} style={{ color: 'rgb(var(--hyper-danger))' }} />
        </div>
        <p css={tw`font-semibold text-base`} style={{ color: 'rgb(var(--hyper-text))' }}>
            {title}
        </p>
        {message && (
            <p css={tw`text-sm mt-1 max-w-md`} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                {message}
            </p>
        )}
        {onRetry && (
            <div css={tw`mt-5`}>
                <Button onClick={onRetry}>Try Again</Button>
            </div>
        )}
    </div>
);

export default ErrorState;
