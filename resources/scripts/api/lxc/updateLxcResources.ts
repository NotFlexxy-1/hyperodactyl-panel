import http from '@/api/http';
import { LxcContainer, rawDataToLxcContainer } from '@/api/lxc/types';

export interface LxcResourceUpdate {
    memory?: number;
    swap?: number;
    disk?: number;
    cpu_limit?: number;
    io_weight?: number;
}

export default (uuid: string, values: LxcResourceUpdate): Promise<LxcContainer> => {
    return new Promise((resolve, reject) => {
        http.patch(`/api/client/lxc/${uuid}`, values)
            .then(({ data }) => resolve(rawDataToLxcContainer(data.attributes)))
            .catch(reject);
    });
};
