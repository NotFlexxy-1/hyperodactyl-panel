export interface HyperTransaction {
    id: number;
    amount: number;
    type: string;
    description: string | null;
    balanceAfter: number;
    createdAt: Date | null;
}

export interface HyperWallet {
    balance: number;
    transactions: HyperTransaction[];
    pagination: {
        total: number;
        count: number;
        perPage: number;
        currentPage: number;
        totalPages: number;
    };
}

export interface HyperStoreItem {
    id: number;
    name: string;
    description: string | null;
    category: string;
    icon: string | null;
    price: number;
    effect: Record<string, any> | null;
    enabled: boolean;
    stock: number | null;
}

export interface HyperPurchase {
    id: number;
    itemId: number;
    itemName: string | null;
    serverName: string | null;
    pricePaid: number;
    status: string;
    createdAt: Date | null;
}

export interface HyperAchievement {
    id: number;
    key: string;
    name: string;
    description: string | null;
    icon: string | null;
    coinReward: number;
    unlocked: boolean;
    unlockedAt: Date | null;
}

export interface HyperRewardStatus {
    dailyAvailableAt: Date | null;
    hourlyAvailableAt: Date | null;
}

export const rawDataToTransaction = (data: Record<string, any>): HyperTransaction => ({
    id: data.id,
    amount: data.amount,
    type: data.type,
    description: data.description ?? null,
    balanceAfter: data.balance_after ?? 0,
    createdAt: data.created_at ? new Date(data.created_at) : null,
});

export const rawDataToStoreItem = (data: Record<string, any>): HyperStoreItem => ({
    id: data.id,
    name: data.name,
    description: data.description ?? null,
    category: data.category,
    icon: data.icon ?? null,
    price: data.price,
    effect: data.effect ?? null,
    enabled: !!data.enabled,
    stock: data.stock ?? null,
});

export const rawDataToPurchase = (data: Record<string, any>): HyperPurchase => ({
    id: data.id,
    itemId: data.item_id,
    itemName: data.item?.name ?? null,
    serverName: data.server?.name ?? null,
    pricePaid: data.price_paid,
    status: data.status,
    createdAt: data.created_at ? new Date(data.created_at) : null,
});

export const rawDataToAchievement = (data: Record<string, any>): HyperAchievement => ({
    id: data.id,
    key: data.key,
    name: data.name,
    description: data.description ?? null,
    icon: data.icon ?? null,
    coinReward: data.coin_reward ?? 0,
    unlocked: !!data.unlocked,
    unlockedAt: data.unlocked_at ? new Date(data.unlocked_at) : null,
});
