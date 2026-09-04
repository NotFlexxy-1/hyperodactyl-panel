import React, { memo, useEffect, useRef, useState } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faEthernet, faHdd, faMemory, faMicrochip, faServer } from '@fortawesome/free-solid-svg-icons';
import { Link } from 'react-router-dom';
import { Server } from '@/api/server/getServer';
import getServerResourceUsage, { ServerPowerState, ServerStats } from '@/api/server/getServerResourceUsage';
import { bytesToString, ip, mbToBytes } from '@/lib/formatters';
import tw from 'twin.macro';
import Spinner from '@/components/elements/Spinner';
import Card from '@/components/elements/Card';
import Badge from '@/components/elements/Badge';
import styled from 'styled-components/macro';
import isEqual from 'react-fast-compare';

// Determines if the current value is in an alarm threshold so we can show it in red rather
// than the more faded default style.
const isAlarmState = (current: number, limit: number): boolean => limit > 0 && current / (limit * 1024 * 1024) >= 0.9;

const StatusBar = styled.div<{ $status: ServerPowerState | undefined }>`
    ${tw`absolute left-0 top-0 h-full w-1 rounded-l-2xl transition-all duration-150`};

    ${({ $status }) =>
        !$status || $status === 'offline'
            ? `background-color: rgb(var(--hyper-danger));`
            : $status === 'running'
            ? `background-color: rgb(var(--hyper-success));`
            : `background-color: rgb(var(--hyper-warning));`};
`;

const Chip = memo(
    ({
        icon,
        label,
        hint,
        alarm,
    }: {
        icon: typeof faMicrochip;
        label: React.ReactNode;
        hint: React.ReactNode;
        alarm: boolean;
    }) => (
        <div css={tw`flex items-center gap-2 min-w-0`}>
            <FontAwesomeIcon
                icon={icon}
                css={tw`text-sm flex-shrink-0`}
                style={{ color: alarm ? 'rgb(var(--hyper-danger))' : 'rgb(var(--hyper-text-subtle))' }}
            />
            <div css={tw`min-w-0`}>
                <p
                    css={tw`text-sm font-medium truncate`}
                    style={{ color: alarm ? 'rgb(var(--hyper-danger))' : 'rgb(var(--hyper-text))' }}
                >
                    {label}
                </p>
                <p css={tw`text-2xs truncate`} style={{ color: 'rgb(var(--hyper-text-subtle))' }}>
                    {hint}
                </p>
            </div>
        </div>
    ),
    isEqual
);
Chip.displayName = 'Chip';

type Timer = ReturnType<typeof setInterval>;

export default ({ server, className }: { server: Server; className?: string }) => {
    const interval = useRef<Timer>(null) as React.MutableRefObject<Timer>;
    const [isSuspended, setIsSuspended] = useState(server.status === 'suspended');
    const [stats, setStats] = useState<ServerStats | null>(null);

    const getStats = () =>
        getServerResourceUsage(server.uuid)
            .then((data) => setStats(data))
            .catch((error) => console.error(error));

    useEffect(() => {
        setIsSuspended(stats?.isSuspended || server.status === 'suspended');
    }, [stats?.isSuspended, server.status]);

    useEffect(() => {
        // Don't waste a HTTP request if there is nothing important to show to the user because
        // the server is suspended.
        if (isSuspended || server.isNodeUnderMaintenance) return;

        getStats().then(() => {
            interval.current = setInterval(() => getStats(), 30000);
        });

        return () => {
            interval.current && clearInterval(interval.current);
        };
    }, [isSuspended, server.isNodeUnderMaintenance]);

    const alarms = { cpu: false, memory: false, disk: false };
    if (stats) {
        alarms.cpu = server.limits.cpu === 0 ? false : stats.cpuUsagePercent >= server.limits.cpu * 0.9;
        alarms.memory = isAlarmState(stats.memoryUsageInBytes, server.limits.memory);
        alarms.disk = server.limits.disk === 0 ? false : isAlarmState(stats.diskUsageInBytes, server.limits.disk);
    }

    const diskLimit = server.limits.disk !== 0 ? bytesToString(mbToBytes(server.limits.disk)) : 'Unlimited';
    const memoryLimit = server.limits.memory !== 0 ? bytesToString(mbToBytes(server.limits.memory)) : 'Unlimited';
    const cpuLimit = server.limits.cpu !== 0 ? server.limits.cpu + ' %' : 'Unlimited';

    const defaultAllocation = server.allocations.find((alloc) => alloc.isDefault);

    return (
        <Card
            as={Link}
            to={`/server/${server.id}`}
            $hoverable
            className={className}
            css={tw`relative flex flex-col md:flex-row md:items-center gap-4 pl-6 no-underline`}
        >
            <StatusBar $status={stats?.status} />
            <div css={tw`flex items-center gap-4 min-w-0 md:w-1/3`}>
                <div
                    css={tw`w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0`}
                    style={{ backgroundColor: 'rgb(var(--hyper-brand) / 0.14)' }}
                >
                    <FontAwesomeIcon icon={faServer} style={{ color: 'rgb(var(--hyper-brand-400))' }} />
                </div>
                <div css={tw`min-w-0`}>
                    <p css={tw`font-medium truncate`} style={{ color: 'rgb(var(--hyper-text))' }}>
                        {server.name}
                    </p>
                    {!!server.description ? (
                        <p css={tw`text-xs truncate`} style={{ color: 'rgb(var(--hyper-text-subtle))' }}>
                            {server.description}
                        </p>
                    ) : defaultAllocation ? (
                        <p css={tw`text-xs flex items-center gap-1.5 truncate`} style={{ color: 'rgb(var(--hyper-text-subtle))' }}>
                            <FontAwesomeIcon icon={faEthernet} />
                            {defaultAllocation.alias || ip(defaultAllocation.ip)}:{defaultAllocation.port}
                        </p>
                    ) : null}
                </div>
            </div>

            <div css={tw`flex-1 grid grid-cols-3 gap-3 sm:gap-4`}>
                {!stats || isSuspended || server.isNodeUnderMaintenance ? (
                    <div css={tw`col-span-3 flex items-center justify-center sm:justify-start`}>
                        {isSuspended ? (
                            <Badge variant={'danger'}>
                                {server.status === 'suspended' ? 'Suspended' : 'Connection Error'}
                            </Badge>
                        ) : server.isNodeUnderMaintenance ? (
                            <Badge variant={'warning'}>Under Maintenance</Badge>
                        ) : server.isTransferring || server.status ? (
                            <Badge variant={'default'}>
                                {server.isTransferring
                                    ? 'Transferring'
                                    : server.status === 'installing'
                                    ? 'Installing'
                                    : server.status === 'restoring_backup'
                                    ? 'Restoring Backup'
                                    : 'Unavailable'}
                            </Badge>
                        ) : (
                            <Spinner size={'small'} />
                        )}
                    </div>
                ) : (
                    <>
                        <Chip icon={faMicrochip} label={`${stats.cpuUsagePercent.toFixed(1)}%`} hint={`of ${cpuLimit}`} alarm={alarms.cpu} />
                        <Chip
                            icon={faMemory}
                            label={bytesToString(stats.memoryUsageInBytes)}
                            hint={`of ${memoryLimit}`}
                            alarm={alarms.memory}
                        />
                        <Chip
                            icon={faHdd}
                            label={bytesToString(stats.diskUsageInBytes)}
                            hint={`of ${diskLimit}`}
                            alarm={alarms.disk}
                        />
                    </>
                )}
            </div>
        </Card>
    );
};
