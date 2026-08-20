import http from '@/api/http';
import { LxcSnapshot } from '@/api/lxc/types';

/**
 * Snapshot payloads differ between drivers: LXD returns objects keyed by index
 * containing `name` (a full API path) and `created_at`, Proxmox returns objects
 * with `name`/`snaptime`. Normalize both into a single shape.
 */
const normalize = (raw: any): LxcSnapshot => {
    const name = String(raw?.name ?? raw ?? '').split('/').pop() as string;
    const created = raw?.created_at ?? raw?.snaptime ?? null;

    return {
        name,
        createdAt: created ? new Date(typeof created === 'number' ? created * 1000 : created) : null,
    };
};

export const getLxcSnapshots = (uuid: string): Promise<LxcSnapshot[]> => {
    return new Promise((resolve, reject) => {
        http.get(`/api/client/lxc/${uuid}/snapshots`)
            .then(({ data }) => {
                const list = Array.isArray(data) ? data : Object.values(data || {});

                resolve(list.map(normalize).filter((snapshot) => !!snapshot.name && snapshot.name !== 'current'));
            })
            .catch(reject);
    });
};

export const createLxcSnapshot = (uuid: string, name: string): Promise<void> => {
    return new Promise((resolve, reject) => {
        http.post(`/api/client/lxc/${uuid}/snapshots`, { name })
            .then(() => resolve())
            .catch(reject);
    });
};

export const restoreLxcSnapshot = (uuid: string, name: string): Promise<void> => {
    return new Promise((resolve, reject) => {
        http.post(`/api/client/lxc/${uuid}/snapshots/${encodeURIComponent(name)}/restore`)
            .then(() => resolve())
            .catch(reject);
    });
};
