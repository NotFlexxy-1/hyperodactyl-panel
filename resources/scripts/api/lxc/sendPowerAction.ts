import http from '@/api/http';
import { LxcPowerAction } from '@/api/lxc/types';

export default (uuid: string, action: LxcPowerAction): Promise<void> => {
    return new Promise((resolve, reject) => {
        http.post(`/api/client/lxc/${uuid}/power`, { action })
            .then(() => resolve())
            .catch(reject);
    });
};
