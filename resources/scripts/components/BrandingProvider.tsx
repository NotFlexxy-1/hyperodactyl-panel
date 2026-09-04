import { useEffect } from 'react';
import getBranding, { BrandingSettings } from '@/api/branding/getBranding';

const COLOR_KEYS: (keyof BrandingSettings)[] = [
    'color_primary',
    'color_accent',
    'color_background',
    'color_surface',
    'color_text',
    'color_danger',
    'color_success',
];

export function applyBrandingVariables(branding: Partial<BrandingSettings>): void {
    const root = document.documentElement;
    COLOR_KEYS.forEach((key) => {
        const value = branding[key];
        if (value) {
            root.style.setProperty(`--hyper-${String(key).replace('color_', '')}`, String(value));
        }
    });
    if (branding.border_radius) root.style.setProperty('--hyper-radius', branding.border_radius);
    if (branding.font) root.style.setProperty('--hyper-font', branding.font);
}

/**
 * Fetches the current branding/theme settings from the API and applies them
 * as CSS custom properties on the document root so the SPA reflects any
 * admin-configured theme without a full page reload.
 */
export default () => {
    useEffect(() => {
        getBranding()
            .then((branding) => applyBrandingVariables(branding))
            .catch(() => {
                // Silently ignore — falls back to server-rendered CSS variables.
            });
    }, []);

    return null;
};
