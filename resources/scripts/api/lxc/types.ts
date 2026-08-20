export interface LxcAllocation {
    id: number;
    protocol: string;
    hostPort: number;
    containerPort: number;
}

export interface LxcContainer {
    uuid: string;
    identifier: string;
    name: string;
    description: string | null;
    status: string | null;
    image: string;
    limits: {
        memory: number;
        swap: number;
        disk: number;
        cpu: number;
        io: number;
    };
    ipAddress: string | null;
    node: {
        uuid: string;
        name: string;
        driver: string;
    };
    allocations: LxcAllocation[];
    installedAt: Date | null;
    createdAt: Date | null;
}

export interface LxcResources {
    status: string;
    cpuUsageNs: number;
    memoryUsage: number;
    memoryLimit: number;
    diskUsage: number;
    network: Record<string, unknown>;
}

export interface LxcSnapshot {
    name: string;
    createdAt: Date | null;
}

export type LxcPowerAction = 'start' | 'stop' | 'restart' | 'freeze';

export const rawDataToLxcContainer = (data: Record<string, any>): LxcContainer => ({
    uuid: data.uuid,
    identifier: data.identifier,
    name: data.name,
    description: data.description ?? null,
    status: data.status ?? null,
    image: data.image,
    limits: {
        memory: data.limits?.memory ?? 0,
        swap: data.limits?.swap ?? 0,
        disk: data.limits?.disk ?? 0,
        cpu: data.limits?.cpu ?? 0,
        io: data.limits?.io ?? 0,
    },
    ipAddress: data.ip_address ?? null,
    node: {
        uuid: data.node?.uuid ?? '',
        name: data.node?.name ?? '',
        driver: data.node?.driver ?? '',
    },
    allocations: (data.allocations || []).map((allocation: Record<string, any>) => ({
        id: allocation.id,
        protocol: allocation.protocol,
        hostPort: allocation.host_port,
        containerPort: allocation.container_port,
    })),
    installedAt: data.installed_at ? new Date(data.installed_at) : null,
    createdAt: data.created_at ? new Date(data.created_at) : null,
});
