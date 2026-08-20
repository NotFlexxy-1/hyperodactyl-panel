import http from '@/api/http';
import { LxcContainer, rawDataToLxcContainer } from '@/api/lxc/types';

export default (uuid: string): Promise<LxcContainer> => {
    return new Promise((resolve, reject) => {
        http.get(`/api/client/lxc/${uuid}`)
            .then(({ data }) => resolve(rawDataToLxcContainer(data.attributes)))
            .catch(reject);
    });
};
