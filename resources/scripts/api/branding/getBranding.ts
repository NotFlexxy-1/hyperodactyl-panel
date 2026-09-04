import http from '@/api/http';

export interface BrandingSettings {
    site_name: string;
    site_short_name: string;
    logo_url: string;
    favicon_url: string;
    social_description: string;
    meta_keywords: string;
    og_image_url: string;
    color_primary: string;
    color_accent: string;
    color_background: string;
    color_surface: string;
    color_text: string;
    color_danger: string;
    color_success: string;
    border_radius: string;
    font: string;
    sidebar_layout: { key: string; label: string; icon: string; enabled: boolean; order: number }[];
    dashboard_widgets: { key: string; enabled: boolean; order: number }[];
    allow_user_theme_override: boolean;
    user_customizable_keys: string[];
}

export default (): Promise<BrandingSettings> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/branding')
            .then(({ data }) => resolve(data.attributes))
            .catch(reject);
    });
};
