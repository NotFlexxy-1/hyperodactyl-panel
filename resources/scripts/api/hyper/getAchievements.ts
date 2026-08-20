import http from '@/api/http';
import { HyperAchievement, rawDataToAchievement } from '@/api/hyper/types';

export default (): Promise<HyperAchievement[]> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/hyper/achievements')
            .then(({ data }) => resolve((data.data || []).map((item: any) => rawDataToAchievement(item.attributes))))
            .catch(reject);
    });
};
