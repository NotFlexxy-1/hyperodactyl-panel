import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import tw from 'twin.macro';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faBox, faMicrochip, faMemory, faHdd, faNetworkWired } from '@fortawesome/free-solid-svg-icons';
import PageContentBlock from '@/components/elements/PageContentBlock';
import PageHeader from '@/components/elements/PageHeader';
import Card from '@/components/elements/Card';
import Badge from '@/components/elements/Badge';
import Spinner from '@/components/elements/Spinner';
import EmptyState from '@/components/elements/EmptyState';
import ErrorState from '@/components/elements/ErrorState';
import getLxcContainers from '@/api/lxc/getLxcContainers';
import { LxcContainer } from '@/api/lxc/types';
import { statusVariant } from '@/components/lxc/status';
import { bytesToString, mbToBytes } from '@/lib/formatters';

export default () => {
    const [containers, setContainers] = useState<LxcContainer[] | null>(null);
    const [error, setError] = useState<string | null>(null);

    const load = () => {
        setError(null);
        getLxcContainers()
            .then(setContainers)
            .catch((e) => setError(e?.message || 'Unable to load LXC containers.'));
    };

    useEffect(load, []);

    return (
        <PageContentBlock title={'LXC Containers'}>
            <PageHeader
                title={'LXC Containers'}
                description={'Linux containers provisioned for your account across the configured LXD / Proxmox nodes.'}
            />
            {error ? (
                <ErrorState message={error} onRetry={load} />
            ) : !containers ? (
                <Spinner centered size={'large'} />
            ) : containers.length === 0 ? (
                <EmptyState
                    icon={faBox}
                    title={'No LXC containers'}
                    description={'You do not currently have any LXC containers assigned to your account.'}
                />
            ) : (
                <div css={tw`grid gap-4 md:grid-cols-2`}>
                    {containers.map((container) => (
                        <Link key={container.uuid} to={`/lxc/${container.uuid}`} css={tw`no-underline`}>
                            <Card $hoverable>
                                <div css={tw`flex items-start justify-between gap-3`}>
                                    <div css={tw`min-w-0`}>
                                        <p
                                            css={tw`font-semibold text-base truncate`}
                                            style={{ color: 'rgb(var(--hyper-text))' }}
                                        >
                                            {container.name}
                                        </p>
                                        <p css={tw`text-xs mt-1`} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                                            {container.image} &middot; {container.node.name} ({container.node.driver})
                                        </p>
                                    </div>
                                    <Badge variant={statusVariant(container.status)}>
                                        {container.status || 'unknown'}
                                    </Badge>
                                </div>
                                <div
                                    css={tw`grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 text-xs`}
                                    style={{ color: 'rgb(var(--hyper-text-muted))' }}
                                >
                                    <span>
                                        <FontAwesomeIcon icon={faMemory} css={tw`mr-1.5`} />
                                        {bytesToString(mbToBytes(container.limits.memory))}
                                    </span>
                                    <span>
                                        <FontAwesomeIcon icon={faHdd} css={tw`mr-1.5`} />
                                        {bytesToString(mbToBytes(container.limits.disk))}
                                    </span>
                                    <span>
                                        <FontAwesomeIcon icon={faMicrochip} css={tw`mr-1.5`} />
                                        {container.limits.cpu === 0 ? 'Unlimited' : `${container.limits.cpu}%`}
                                    </span>
                                    <span css={tw`truncate`}>
                                        <FontAwesomeIcon icon={faNetworkWired} css={tw`mr-1.5`} />
                                        {container.ipAddress || 'No IP'}
                                    </span>
                                </div>
                            </Card>
                        </Link>
                    ))}
                </div>
            )}
        </PageContentBlock>
    );
};
