import http from '@/api/http';
import { HyperWallet, rawDataToTransaction } from '@/api/hyper/types';

export default (page = 1): Promise<HyperWallet> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/hyper/wallet', { params: { page } })
            .then(({ data }) => {
                const pagination = data.meta?.pagination || {};

                resolve({
                    balance: data.attributes?.balance ?? 0,
                    transactions: (data.transactions || []).map((item: any) => rawDataToTransaction(item.attributes)),
                    pagination: {
                        total: pagination.total ?? 0,
                        count: pagination.count ?? 0,
                        perPage: pagination.per_page ?? 25,
                        currentPage: pagination.current_page ?? 1,
                        totalPages: pagination.total_pages ?? 1,
                    },
                });
            })
            .catch(reject);
    });
};
