import React, { useEffect, useState } from 'react';
import tw from 'twin.macro';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faTrophy, faLock, faCheck } from '@fortawesome/free-solid-svg-icons';
import PageContentBlock from '@/components/elements/PageContentBlock';
import PageHeader from '@/components/elements/PageHeader';
import Card from '@/components/elements/Card';
import Badge from '@/components/elements/Badge';
import Spinner from '@/components/elements/Spinner';
import EmptyState from '@/components/elements/EmptyState';
import FlashMessageRender from '@/components/FlashMessageRender';
import { useFlashKey } from '@/plugins/useFlash';
import getAchievements from '@/api/hyper/getAchievements';
import { HyperAchievement } from '@/api/hyper/types';

export default () => {
    const [achievements, setAchievements] = useState<HyperAchievement[] | null>(null);
    const { clearAndAddHttpError } = useFlashKey('hyper:achievements');

    useEffect(() => {
        getAchievements()
            .then(setAchievements)
            .catch((error) => {
                setAchievements([]);
                clearAndAddHttpError(error);
            });
    }, []);

    const unlocked = (achievements || []).filter((a) => a.unlocked).length;

    return (
        <PageContentBlock title={'Achievements'}>
            <FlashMessageRender byKey={'hyper:achievements'} css={tw`mb-4`} />
            <PageHeader
                title={'Achievements'}
                description={'Milestones evaluated against your real account activity.'}
                actions={
                    achievements ? (
                        <Badge variant={'brand'}>
                            {unlocked} / {achievements.length} unlocked
                        </Badge>
                    ) : undefined
                }
            />
            {!achievements ? (
                <Spinner centered size={'large'} />
            ) : achievements.length === 0 ? (
                <EmptyState
                    icon={faTrophy}
                    title={'No achievements configured'}
                    description={'An administrator has not defined any achievements yet.'}
                />
            ) : (
                <div css={tw`grid gap-4 md:grid-cols-2 lg:grid-cols-3`}>
                    {achievements.map((achievement) => (
                        <Card key={achievement.id} css={tw`flex gap-4`} style={{ opacity: achievement.unlocked ? 1 : 0.7 }}>
                            <div
                                css={tw`w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0`}
                                style={{
                                    backgroundColor: achievement.unlocked
                                        ? 'rgb(var(--hyper-success) / 0.16)'
                                        : 'rgb(var(--hyper-surface-3))',
                                }}
                            >
                                <FontAwesomeIcon
                                    icon={achievement.unlocked ? faCheck : faLock}
                                    style={{
                                        color: achievement.unlocked
                                            ? 'rgb(var(--hyper-success))'
                                            : 'rgb(var(--hyper-text-subtle))',
                                    }}
                                />
                            </div>
                            <div css={tw`min-w-0`}>
                                <p css={tw`font-semibold`} style={{ color: 'rgb(var(--hyper-text))' }}>
                                    {achievement.name}
                                </p>
                                {achievement.description && (
                                    <p css={tw`text-sm mt-1`} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                                        {achievement.description}
                                    </p>
                                )}
                                <div css={tw`flex items-center gap-2 mt-3 flex-wrap`}>
                                    <Badge variant={'brand'}>+{achievement.coinReward} coins</Badge>
                                    {achievement.unlocked && achievement.unlockedAt && (
                                        <span
                                            css={tw`text-2xs`}
                                            style={{ color: 'rgb(var(--hyper-text-subtle))' }}
                                        >
                                            {achievement.unlockedAt.toLocaleDateString()}
                                        </span>
                                    )}
                                </div>
                            </div>
                        </Card>
                    ))}
                </div>
            )}
        </PageContentBlock>
    );
};
