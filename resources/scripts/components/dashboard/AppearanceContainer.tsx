import React, { useEffect, useState } from 'react';
import PageContentBlock from '@/components/elements/PageContentBlock';
import ContentBox from '@/components/elements/ContentBox';
import FlashMessageRender from '@/components/FlashMessageRender';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import tw from 'twin.macro';
import { useFlashKey } from '@/plugins/useFlash';
import getAppearance, { AppearanceSettings } from '@/api/account/getAppearance';
import updateAppearance from '@/api/account/updateAppearance';
import { applyBrandingVariables } from '@/components/BrandingProvider';

const LABELS: Record<string, string> = {
    color_primary: 'Primary Color',
    color_accent: 'Accent Color',
    color_background: 'Background Color',
    color_surface: 'Surface Color',
    color_text: 'Text Color',
    color_danger: 'Danger Color',
    color_success: 'Success Color',
};

export default () => {
    const [settings, setSettings] = useState<AppearanceSettings | null>(null);
    const [preferences, setPreferences] = useState<Record<string, string>>({});
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const { clearAndAddHttpError, clearFlashes, addFlash } = useFlashKey('account:appearance');

    useEffect(() => {
        getAppearance()
            .then((data) => {
                setSettings(data);
                setPreferences(data.preferences || {});
            })
            .catch((error) => clearAndAddHttpError(error))
            .then(() => setLoading(false));
    }, []);

    const save = () => {
        setSaving(true);
        clearFlashes();
        updateAppearance(preferences)
            .then((saved) => {
                setPreferences(saved);
                applyBrandingVariables(saved as any);
                addFlash({ type: 'success', key: 'account:appearance', message: 'Appearance preferences saved.' });
            })
            .catch((error) => clearAndAddHttpError(error))
            .then(() => setSaving(false));
    };

    return (
        <PageContentBlock title={'Appearance'}>
            <FlashMessageRender byKey={'account:appearance'} />
            <ContentBox title={'Appearance'} css={tw`relative`}>
                <SpinnerOverlay visible={loading} />
                {!loading && settings && !settings.allow_user_theme_override && (
                    <p css={tw`text-sm text-neutral-300`}>
                        Your administrator has not enabled custom theme overrides for your account.
                    </p>
                )}
                {!loading && settings && settings.allow_user_theme_override && (
                    <>
                        {settings.user_customizable_keys.length === 0 ? (
                            <p css={tw`text-sm text-neutral-300`}>
                                There are no customizable appearance options available to you right now.
                            </p>
                        ) : (
                            <div css={tw`grid grid-cols-1 md:grid-cols-2 gap-4`}>
                                {settings.user_customizable_keys.map((key) => (
                                    <div key={key}>
                                        <label css={tw`text-sm block mb-1`}>{LABELS[key] || key}</label>
                                        <div css={tw`flex items-center`}>
                                            <input
                                                type={'color'}
                                                value={preferences[key] || '#5b8cff'}
                                                onChange={(e) =>
                                                    setPreferences((s) => ({ ...s, [key]: e.target.value }))
                                                }
                                                css={tw`mr-2 w-10 h-8 border-none`}
                                            />
                                            <input
                                                type={'text'}
                                                value={preferences[key] || ''}
                                                onChange={(e) =>
                                                    setPreferences((s) => ({ ...s, [key]: e.target.value }))
                                                }
                                                css={tw`bg-neutral-600 rounded p-2 text-sm w-full`}
                                            />
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                        <button
                            css={tw`mt-6 bg-primary-500 hover:bg-primary-600 text-white rounded px-4 py-2 text-sm disabled:opacity-50`}
                            disabled={saving || settings.user_customizable_keys.length === 0}
                            onClick={save}
                        >
                            {saving ? 'Saving...' : 'Save Appearance'}
                        </button>
                    </>
                )}
            </ContentBox>
        </PageContentBlock>
    );
};
