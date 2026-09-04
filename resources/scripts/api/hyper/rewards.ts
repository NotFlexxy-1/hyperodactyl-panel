import http from '@/api/http';
import { HyperRewardStatus, HyperTransaction, rawDataToTransaction } from '@/api/hyper/types';

export const getRewardStatus = (): Promise<HyperRewardStatus> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/hyper/rewards')
            .then(({ data }) =>
                resolve({
                    dailyAvailableAt: data.attributes?.daily_available_at
                        ? new Date(data.attributes.daily_available_at)
                        : null,
                    hourlyAvailableAt: data.attributes?.hourly_available_at
                        ? new Date(data.attributes.hourly_available_at)
                        : null,
                })
            )
            .catch(reject);
    });
};

export const claimReward = (kind: 'daily' | 'hourly' = 'daily'): Promise<HyperTransaction> => {
    return new Promise((resolve, reject) => {
        http.post('/api/client/hyper/rewards/claim', { kind })
            .then(({ data }) => resolve(rawDataToTransaction(data.attributes)))
            .catch(reject);
    });
};
