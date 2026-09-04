import http from '@/api/http';

export interface AppearanceSettings {
    preferences: Record<string, string>;
    allow_user_theme_override: boolean;
    user_customizable_keys: string[];
}

export default (): Promise<AppearanceSettings> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/account/appearance')
            .then(({ data }) => resolve(data.attributes))
            .catch(reject);
    });
};
