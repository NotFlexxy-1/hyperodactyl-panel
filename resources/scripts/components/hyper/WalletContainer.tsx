import React, { useEffect, useState } from 'react';
import tw from 'twin.macro';
import { faCoins, faGift, faReceipt } from '@fortawesome/free-solid-svg-icons';
import PageContentBlock from '@/components/elements/PageContentBlock';
import PageHeader from '@/components/elements/PageHeader';
import Card, { CardTitle } from '@/components/elements/Card';
import Badge from '@/components/elements/Badge';
import Button from '@/components/elements/Button';
import StatCard from '@/components/elements/StatCard';
import Spinner from '@/components/elements/Spinner';
import EmptyState from '@/components/elements/EmptyState';
import Pagination from '@/components/elements/Pagination';
import FlashMessageRender from '@/components/FlashMessageRender';
import { useFlashKey } from '@/plugins/useFlash';
import getWallet from '@/api/hyper/getWallet';
import { claimReward, getRewardStatus } from '@/api/hyper/rewards';
import { HyperRewardStatus, HyperWallet } from '@/api/hyper/types';

const typeVariant = (type: string) => {
    switch (type) {
        case 'spend':
        case 'purchase':
            return 'danger' as const;
        case 'earn':
        case 'reward':
            return 'success' as const;
        case 'admin_grant':
            return 'brand' as const;
        default:
            return 'default' as const;
    }
};

export default () => {
    const [page, setPage] = useState(1);
    const [wallet, setWallet] = useState<HyperWallet | null>(null);
    const [rewards, setRewards] = useState<HyperRewardStatus | null>(null);
    const [claiming, setClaiming] = useState(false);
    const { clearFlashes, clearAndAddHttpError, addFlash } = useFlashKey('hyper:wallet');

    const load = (target = page) => {
        getWallet(target)
            .then(setWallet)
            .catch((error) => clearAndAddHttpError(error));
        getRewardStatus()
            .then(setRewards)
            .catch(() => setRewards(null));
    };

    useEffect(() => {
        load(page);
    }, [page]);

    const claim = (kind: 'daily' | 'hourly') => {
        setClaiming(true);
        clearFlashes();
        claimReward(kind)
            .then((transaction) => {
                addFlash({
                    type: 'success',
                    message: `Claimed ${transaction.amount} Hyper Coins.`,
                });
                load(1);
                setPage(1);
            })
            .catch((error) => clearAndAddHttpError(error))
            .then(() => setClaiming(false));
    };

    const claimable = (at: Date | null) => !at || at.getTime() <= Date.now();

    return (
        <PageContentBlock title={'Hyper Coin Wallet'}>
            <FlashMessageRender byKey={'hyper:wallet'} css={tw`mb-4`} />
            <PageHeader title={'Wallet'} description={'Your Hyper Coin balance and full transaction history.'} />
            {!wallet ? (
                <Spinner centered size={'large'} />
            ) : (
                <>
                    <div css={tw`grid gap-4 sm:grid-cols-3 mb-6`}>
                        <StatCard icon={faCoins} label={'Balance'} value={wallet.balance.toLocaleString()} />
                        <StatCard
                            icon={faReceipt}
                            label={'Transactions'}
                            value={wallet.pagination.total.toLocaleString()}
                        />
                        <Card css={tw`flex flex-col justify-center gap-2`}>
                            <CardTitle>Rewards</CardTitle>
                            <div css={tw`flex gap-2 flex-wrap`}>
                                <Button
                                    size={'xsmall'}
                                    disabled={claiming || !rewards || !claimable(rewards.dailyAvailableAt)}
                                    onClick={() => claim('daily')}
                                >
                                    Claim daily
                                </Button>
                                <Button
                                    size={'xsmall'}
                                    isSecondary
                                    disabled={claiming || !rewards || !claimable(rewards.hourlyAvailableAt)}
                                    onClick={() => claim('hourly')}
                                >
                                    Claim hourly
                                </Button>
                            </div>
                            {rewards && (
                                <p css={tw`text-2xs`} style={{ color: 'rgb(var(--hyper-text-subtle))' }}>
                                    Next daily:{' '}
                                    {rewards.dailyAvailableAt ? rewards.dailyAvailableAt.toLocaleString() : 'now'}
                                </p>
                            )}
                        </Card>
                    </div>
                    <Card>
                        <CardTitle>Transaction history</CardTitle>
                        {wallet.transactions.length === 0 ? (
                            <EmptyState
                                icon={faGift}
                                title={'No transactions yet'}
                                description={'Earn coins through rewards and achievements to get started.'}
                            />
                        ) : (
                            <div css={tw`divide-y`} style={{ borderColor: 'rgb(var(--hyper-border))' }}>
                                {wallet.transactions.map((transaction) => (
                                    <div key={transaction.id} css={tw`flex items-center justify-between gap-3 py-3`}>
                                        <div css={tw`min-w-0`}>
                                            <p
                                                css={tw`font-medium truncate`}
                                                style={{ color: 'rgb(var(--hyper-text))' }}
                                            >
                                                {transaction.description || transaction.type}
                                            </p>
                                            <p
                                                css={tw`text-xs mt-1`}
                                                style={{ color: 'rgb(var(--hyper-text-muted))' }}
                                            >
                                                {transaction.createdAt
                                                    ? transaction.createdAt.toLocaleString()
                                                    : ''}{' '}
                                                &middot; balance {transaction.balanceAfter.toLocaleString()}
                                            </p>
                                        </div>
                                        <div css={tw`flex items-center gap-3`}>
                                            <Badge variant={typeVariant(transaction.type)}>{transaction.type}</Badge>
                                            <span
                                                css={tw`font-semibold`}
                                                style={{
                                                    color:
                                                        transaction.amount < 0
                                                            ? 'rgb(var(--hyper-danger))'
                                                            : 'rgb(var(--hyper-success))',
                                                }}
                                            >
                                                {transaction.amount > 0 ? '+' : ''}
                                                {transaction.amount.toLocaleString()}
                                            </span>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                        {wallet.pagination.totalPages > 1 && (
                            <div css={tw`mt-4 flex items-center justify-center gap-2`}>
                                <Button
                                    size={'xsmall'}
                                    isSecondary
                                    disabled={page <= 1}
                                    onClick={() => setPage((p) => Math.max(1, p - 1))}
                                >
                                    Previous
                                </Button>
                                <span css={tw`text-xs`} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                                    Page {wallet.pagination.currentPage} of {wallet.pagination.totalPages}
                                </span>
                                <Button
                                    size={'xsmall'}
                                    isSecondary
                                    disabled={page >= wallet.pagination.totalPages}
                                    onClick={() => setPage((p) => p + 1)}
                                >
                                    Next
                                </Button>
                            </div>
                        )}
                    </Card>
                </>
            )}
        </PageContentBlock>
    );
};
