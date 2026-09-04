import http from '@/api/http';
import { HyperPurchase, HyperStoreItem, rawDataToPurchase, rawDataToStoreItem } from '@/api/hyper/types';

export const getStoreItems = (): Promise<HyperStoreItem[]> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/hyper/store')
            .then(({ data }) => resolve((data.data || []).map((item: any) => rawDataToStoreItem(item.attributes))))
            .catch(reject);
    });
};

export const getPurchaseHistory = (): Promise<HyperPurchase[]> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/hyper/store/history')
            .then(({ data }) => resolve((data.data || []).map((item: any) => rawDataToPurchase(item.attributes))))
            .catch(reject);
    });
};

export const purchaseStoreItem = (
    itemId: number,
    serverUuid?: string
): Promise<{ purchase: HyperPurchase; balance: number }> => {
    return new Promise((resolve, reject) => {
        http.post(`/api/client/hyper/store/${itemId}/purchase`, serverUuid ? { server: serverUuid } : {})
            .then(({ data }) =>
                resolve({
                    purchase: rawDataToPurchase(data.attributes),
                    balance: data.balance ?? 0,
                })
            )
            .catch(reject);
    });
};
