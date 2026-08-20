import React from 'react';
import tw from 'twin.macro';
import { faNetworkWired } from '@fortawesome/free-solid-svg-icons';
import PageContentBlock from '@/components/elements/PageContentBlock';
import PageHeader from '@/components/elements/PageHeader';
import Card, { CardTitle } from '@/components/elements/Card';
import Badge from '@/components/elements/Badge';
import EmptyState from '@/components/elements/EmptyState';
import CopyOnClick from '@/components/elements/CopyOnClick';
import { useLxcContainer } from '@/components/lxc/LxcContainerContext';

export default () => {
    const { container } = useLxcContainer();

    return (
        <PageContentBlock title={`Network — ${container.name}`}>
            <PageHeader
                title={'Network'}
                description={'Addressing and port allocations assigned to this container by the panel.'}
            />
            <Card css={tw`mb-4`}>
                <CardTitle>Primary address</CardTitle>
                {container.ipAddress ? (
                    <CopyOnClick text={container.ipAddress}>
                        <p css={tw`text-lg font-semibold cursor-pointer`} style={{ color: 'rgb(var(--hyper-text))' }}>
                            {container.ipAddress}
                        </p>
                    </CopyOnClick>
                ) : (
                    <p css={tw`text-sm`} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                        No address has been reported for this container yet.
                    </p>
                )}
            </Card>
            <Card>
                <CardTitle>Port allocations</CardTitle>
                {container.allocations.length === 0 ? (
                    <EmptyState
                        icon={faNetworkWired}
                        title={'No allocations'}
                        description={'An administrator has not assigned any forwarded ports to this container.'}
                    />
                ) : (
                    <div css={tw`divide-y`} style={{ borderColor: 'rgb(var(--hyper-border))' }}>
                        {container.allocations.map((allocation) => (
                            <div key={allocation.id} css={tw`flex items-center justify-between gap-3 py-3`}>
                                <div>
                                    <CopyOnClick text={`${container.ipAddress ?? ''}:${allocation.hostPort}`}>
                                        <p
                                            css={tw`font-medium cursor-pointer`}
                                            style={{ color: 'rgb(var(--hyper-text))' }}
                                        >
                                            {container.ipAddress ?? 'host'}:{allocation.hostPort}
                                        </p>
                                    </CopyOnClick>
                                    <p css={tw`text-xs mt-1`} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                                        forwards to container port {allocation.containerPort}
                                    </p>
                                </div>
                                <Badge variant={'info'}>{allocation.protocol}</Badge>
                            </div>
                        ))}
                    </div>
                )}
            </Card>
        </PageContentBlock>
    );
};
