import React from 'react';
import tw from 'twin.macro';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconProp } from '@fortawesome/fontawesome-svg-core';
import Card from '@/components/elements/Card';

interface Props {
    icon?: IconProp;
    label: string;
    value: React.ReactNode;
    hint?: React.ReactNode;
    alarm?: boolean;
    className?: string;
}

const StatCard = ({ icon, label, value, hint, alarm, className }: Props) => (
    <Card $padded className={className} css={tw`flex items-center gap-4`}>
        {icon && (
            <div
                css={tw`w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0`}
                style={{
                    backgroundColor: alarm ? 'rgb(var(--hyper-danger) / 0.14)' : 'rgb(var(--hyper-brand) / 0.14)',
                }}
            >
                <FontAwesomeIcon
                    icon={icon}
                    style={{ color: alarm ? 'rgb(var(--hyper-danger))' : 'rgb(var(--hyper-brand-400))' }}
                />
            </div>
        )}
        <div css={tw`min-w-0`}>
            <p css={tw`text-2xs uppercase tracking-wide font-semibold`} style={{ color: 'rgb(var(--hyper-text-subtle))' }}>
                {label}
            </p>
            <p
                css={tw`text-lg font-semibold truncate`}
                style={{ color: alarm ? 'rgb(var(--hyper-danger))' : 'rgb(var(--hyper-text))' }}
            >
                {value}
            </p>
            {hint && (
                <p css={tw`text-2xs mt-0.5`} style={{ color: 'rgb(var(--hyper-text-subtle))' }}>
                    {hint}
                </p>
            )}
        </div>
    </Card>
);

export default StatCard;
