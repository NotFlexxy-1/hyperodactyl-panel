import http from '@/api/http';
import { LxcContainer, rawDataToLxcContainer } from '@/api/lxc/types';

export default (): Promise<LxcContainer[]> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/lxc')
            .then(({ data }) => resolve((data.data || []).map((item: any) => rawDataToLxcContainer(item.attributes))))
            .catch(reject);
    });
};
