import http from '@/api/http';

export interface LxcConsoleDetails {
    // LXD returns a single websocket URL; Proxmox returns ticket based details.
    url: string | null;
    ticket?: string;
    port?: number | string;
    user?: string;
    node?: string;
    upid?: string;
}

export default (uuid: string): Promise<LxcConsoleDetails> => {
    return new Promise((resolve, reject) => {
        http.get(`/api/client/lxc/${uuid}/console`)
            .then(({ data }) => resolve({ ...data, url: data.url ?? null }))
            .catch(reject);
    });
};
