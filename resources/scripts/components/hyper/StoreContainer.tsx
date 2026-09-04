import React, { useEffect, useState } from 'react';
import tw from 'twin.macro';
import { faStore, faCoins } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import PageContentBlock from '@/components/elements/PageContentBlock';
import PageHeader from '@/components/elements/PageHeader';
import Card, { CardTitle } from '@/components/elements/Card';
import Badge from '@/components/elements/Badge';
import Button from '@/components/elements/Button';
import Select from '@/components/elements/Select';
import Spinner from '@/components/elements/Spinner';
import EmptyState from '@/components/elements/EmptyState';
import FlashMessageRender from '@/components/FlashMessageRender';
import { useFlashKey } from '@/plugins/useFlash';
import { getPurchaseHistory, getStoreItems, purchaseStoreItem } from '@/api/hyper/store';
import getWallet from '@/api/hyper/getWallet';
import getServers from '@/api/getServers';
import { HyperPurchase, HyperStoreItem } from '@/api/hyper/types';
import { Server } from '@/api/server/getServer';

export default () => {
    const [items, setItems] = useState<HyperStoreItem[] | null>(null);
    const [history, setHistory] = useState<HyperPurchase[]>([]);
    const [servers, setServers] = useState<Server[]>([]);
    const [balance, setBalance] = useState<number>(0);
    const [target, setTarget] = useState<Record<number, string>>({});
    const [busy, setBusy] = useState<number | null>(null);
    const { clearFlashes, clearAndAddHttpError, addFlash } = useFlashKey('hyper:store');

    const load = () => {
        getStoreItems()
            .then(setItems)
            .catch((error) => {
                setItems([]);
                clearAndAddHttpError(error);
            });
        getPurchaseHistory()
            .then(setHistory)
            .catch(() => undefined);
        getWallet()
            .then((wallet) => setBalance(wallet.balance))
            .catch(() => undefined);
    };

    useEffect(() => {
        load();
        getServers({ page: 1 })
            .then((data) => setServers(data.items))
            .catch(() => undefined);
    }, []);

    const buy = (item: HyperStoreItem) => {
        setBusy(item.id);
        clearFlashes();
        purchaseStoreItem(item.id, target[item.id] || undefined)
            .then(({ balance: updated }) => {
                setBalance(updated);
                addFlash({ type: 'success', message: `Purchased ${item.name}.` });
                load();
            })
            .catch((error) => clearAndAddHttpError(error))
            .then(() => setBusy(null));
    };

    const requiresServer = (item: HyperStoreItem) => item.category === 'resource';

    return (
        <PageContentBlock title={'Hyper Coin Store'}>
            <FlashMessageRender byKey={'hyper:store'} css={tw`mb-4`} />
            <PageHeader
                title={'Store'}
                description={'Spend Hyper Coins on real resource upgrades and account perks.'}
                actions={
                    <Badge variant={'brand'}>
                        <FontAwesomeIcon icon={faCoins} css={tw`mr-1.5`} />
                        {balance.toLocaleString()} coins
                    </Badge>
                }
            />
            {!items ? (
                <Spinner centered size={'large'} />
            ) : items.length === 0 ? (
                <EmptyState
                    icon={faStore}
                    title={'The store is empty'}
                    description={'No store items have been enabled by an administrator yet.'}
                />
            ) : (
                <div css={tw`grid gap-4 md:grid-cols-2 lg:grid-cols-3`}>
                    {items.map((item) => (
                        <Card key={item.id} css={tw`flex flex-col`}>
                            <div css={tw`flex items-start justify-between gap-3`}>
                                <div>
                                    <p css={tw`font-semibold`} style={{ color: 'rgb(var(--hyper-text))' }}>
                                        {item.name}
                                    </p>
                                    <Badge css={tw`mt-2`}>{item.category.replace('_', ' ')}</Badge>
                                </div>
                                <Badge variant={'brand'}>{item.price.toLocaleString()}</Badge>
                            </div>
                            {item.description && (
                                <p css={tw`text-sm mt-3 flex-1`} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                                    {item.description}
                                </p>
                            )}
                            {item.stock !== null && (
                                <p css={tw`text-2xs mt-2`} style={{ color: 'rgb(var(--hyper-text-subtle))' }}>
                                    {item.stock} remaining
                                </p>
                            )}
                            {requiresServer(item) && (
                                <Select
                                    css={tw`mt-3`}
                                    value={target[item.id] || ''}
                                    onChange={(e: React.ChangeEvent<HTMLSelectElement>) => setTarget((s) => ({ ...s, [item.id]: e.target.value }))}
                                >
                                    <option value={''}>Select a server…</option>
                                    {servers.map((server) => (
                                        <option key={server.uuid} value={server.uuid}>
                                            {server.name}
                                        </option>
                                    ))}
                                </Select>
                            )}
                            <Button
                                css={tw`mt-4`}
                                disabled={
                                    busy === item.id ||
                                    balance < item.price ||
                                    (requiresServer(item) && !target[item.id])
                                }
                                onClick={() => buy(item)}
                            >
                                {balance < item.price ? 'Not enough coins' : 'Purchase'}
                            </Button>
                        </Card>
                    ))}
                </div>
            )}
            {history.length > 0 && (
                <Card css={tw`mt-6`}>
                    <CardTitle>Purchase history</CardTitle>
                    <div css={tw`divide-y`} style={{ borderColor: 'rgb(var(--hyper-border))' }}>
                        {history.map((purchase) => (
                            <div key={purchase.id} css={tw`flex items-center justify-between gap-3 py-3`}>
                                <div>
                                    <p css={tw`font-medium`} style={{ color: 'rgb(var(--hyper-text))' }}>
                                        {purchase.itemName || `Item #${purchase.itemId}`}
                                    </p>
                                    <p css={tw`text-xs mt-1`} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                                        {purchase.createdAt ? purchase.createdAt.toLocaleString() : ''}
                                        {purchase.serverName ? ` · ${purchase.serverName}` : ''}
                                    </p>
                                </div>
                                <div css={tw`flex items-center gap-3`}>
                                    <Badge variant={purchase.status === 'completed' ? 'success' : 'warning'}>
                                        {purchase.status}
                                    </Badge>
                                    <span css={tw`font-semibold`} style={{ color: 'rgb(var(--hyper-danger))' }}>
                                        -{purchase.pricePaid.toLocaleString()}
                                    </span>
                                </div>
                            </div>
                        ))}
                    </div>
                </Card>
            )}
        </PageContentBlock>
    );
};
