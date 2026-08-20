import React, { useEffect, useState } from 'react';
import tw from 'twin.macro';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faPlay, faStop, faRedo, faPause, faMemory, faHdd, faMicrochip, faSignal } from '@fortawesome/free-solid-svg-icons';
import PageContentBlock from '@/components/elements/PageContentBlock';
import PageHeader from '@/components/elements/PageHeader';
import Card, { CardTitle } from '@/components/elements/Card';
import Badge from '@/components/elements/Badge';
import Button from '@/components/elements/Button';
import StatCard from '@/components/elements/StatCard';
import FlashMessageRender from '@/components/FlashMessageRender';
import { useFlashKey } from '@/plugins/useFlash';
import { useLxcContainer } from '@/components/lxc/LxcContainerContext';
import { statusVariant } from '@/components/lxc/status';
import getLxcResources from '@/api/lxc/getLxcResources';
import sendPowerAction from '@/api/lxc/sendPowerAction';
import { LxcPowerAction, LxcResources } from '@/api/lxc/types';
import { bytesToString, mbToBytes } from '@/lib/formatters';

export default () => {
    const { container, refresh } = useLxcContainer();
    const { clearAndAddHttpError } = useFlashKey('lxc:overview');
    const [resources, setResources] = useState<LxcResources | null>(null);
    const [busy, setBusy] = useState(false);

    useEffect(() => {
        let cancelled = false;

        const poll = () => {
            getLxcResources(container.uuid)
                .then((data) => {
                    if (!cancelled) setResources(data);
                })
                .catch((error) => clearAndAddHttpError(error));
        };

        poll();
        const interval = setInterval(poll, 10000);

        return () => {
            cancelled = true;
            clearInterval(interval);
        };
    }, [container.uuid]);

    const power = (action: LxcPowerAction) => {
        setBusy(true);
        clearAndAddHttpError();
        sendPowerAction(container.uuid, action)
            .then(() => refresh())
            .catch((error) => clearAndAddHttpError(error))
            .then(() => setBusy(false));
    };

    return (
        <PageContentBlock title={`LXC — ${container.name}`}>
            <FlashMessageRender byKey={'lxc:overview'} css={tw`mb-4`} />
            <PageHeader
                title={container.name}
                description={container.description || `${container.image} on ${container.node.name}`}
                actions={
                    <>
                        <Badge variant={statusVariant(resources?.status ?? container.status)}>
                            {resources?.status ?? container.status ?? 'unknown'}
                        </Badge>
                        <Button size={'small'} color={'green'} disabled={busy} onClick={() => power('start')}>
                            <FontAwesomeIcon icon={faPlay} css={tw`mr-2`} /> Start
                        </Button>
                        <Button size={'small'} isSecondary disabled={busy} onClick={() => power('restart')}>
                            <FontAwesomeIcon icon={faRedo} css={tw`mr-2`} /> Restart
                        </Button>
                        <Button size={'small'} isSecondary disabled={busy} onClick={() => power('freeze')}>
                            <FontAwesomeIcon icon={faPause} css={tw`mr-2`} /> Freeze
                        </Button>
                        <Button size={'small'} color={'red'} disabled={busy} onClick={() => power('stop')}>
                            <FontAwesomeIcon icon={faStop} css={tw`mr-2`} /> Stop
                        </Button>
                    </>
                }
            />
            <div css={tw`grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6`}>
                <StatCard
                    icon={faMicrochip}
                    label={'CPU time'}
                    value={resources ? `${(resources.cpuUsageNs / 1_000_000_000).toFixed(2)}s` : '—'}
                    hint={container.limits.cpu === 0 ? 'Unlimited' : `Limit ${container.limits.cpu}%`}
                />
                <StatCard
                    icon={faMemory}
                    label={'Memory'}
                    value={resources ? bytesToString(resources.memoryUsage) : '—'}
                    hint={`of ${bytesToString(mbToBytes(container.limits.memory))}`}
                />
                <StatCard
                    icon={faHdd}
                    label={'Disk'}
                    value={resources ? bytesToString(resources.diskUsage) : '—'}
                    hint={`of ${bytesToString(mbToBytes(container.limits.disk))}`}
                />
                <StatCard
                    icon={faSignal}
                    label={'Status'}
                    value={resources?.status ?? container.status ?? 'unknown'}
                    hint={`${container.node.driver} node`}
                />
            </div>
            <Card>
                <CardTitle>Container details</CardTitle>
                <div css={tw`grid gap-3 sm:grid-cols-2 text-sm`} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                    <p>
                        UUID: <span style={{ color: 'rgb(var(--hyper-text))' }}>{container.uuid}</span>
                    </p>
                    <p>
                        Short ID: <span style={{ color: 'rgb(var(--hyper-text))' }}>{container.identifier}</span>
                    </p>
                    <p>
                        Image: <span style={{ color: 'rgb(var(--hyper-text))' }}>{container.image}</span>
                    </p>
                    <p>
                        Node:{' '}
                        <span style={{ color: 'rgb(var(--hyper-text))' }}>
                            {container.node.name} ({container.node.driver})
                        </span>
                    </p>
                    <p>
                        Primary IP:{' '}
                        <span style={{ color: 'rgb(var(--hyper-text))' }}>{container.ipAddress || 'Not assigned'}</span>
                    </p>
                    <p>
                        Installed:{' '}
                        <span style={{ color: 'rgb(var(--hyper-text))' }}>
                            {container.installedAt ? container.installedAt.toLocaleString() : 'Pending'}
                        </span>
                    </p>
                </div>
            </Card>
        </PageContentBlock>
    );
};
