import http from '@/api/http';
import { LxcResources } from '@/api/lxc/types';

export default (uuid: string): Promise<LxcResources> => {
    return new Promise((resolve, reject) => {
        http.get(`/api/client/lxc/${uuid}/resources`)
            .then(({ data }) =>
                resolve({
                    status: data.status ?? 'unknown',
                    cpuUsageNs: data.cpu_usage_ns ?? 0,
                    memoryUsage: data.memory_usage ?? 0,
                    memoryLimit: data.memory_limit ?? 0,
                    diskUsage: data.disk_usage ?? 0,
                    network: data.network ?? {},
                })
            )
            .catch(reject);
    });
};
