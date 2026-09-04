import http from '@/api/http';

export default (preferences: Record<string, string>): Promise<Record<string, string>> => {
    return new Promise((resolve, reject) => {
        http.put('/api/client/account/appearance', { preferences })
            .then(({ data }) => resolve(data.attributes.preferences))
            .catch(reject);
    });
};
