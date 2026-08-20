import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import tw from 'twin.macro';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCoins } from '@fortawesome/free-solid-svg-icons';
import getWallet from '@/api/hyper/getWallet';

/**
 * Live Hyper Coin balance pulled from the wallet endpoint. Renders nothing at
 * all if the request fails (for example when the economy tables have not been
 * migrated yet) so the navigation never breaks.
 */
export default ({ className }: { className?: string }) => {
    const [balance, setBalance] = useState<number | null>(null);
    const [failed, setFailed] = useState(false);

    useEffect(() => {
        let cancelled = false;

        const load = () =>
            getWallet()
                .then((wallet) => {
                    if (!cancelled) setBalance(wallet.balance);
                })
                .catch(() => {
                    if (!cancelled) setFailed(true);
                });

        load();
        const interval = setInterval(load, 60000);

        return () => {
            cancelled = true;
            clearInterval(interval);
        };
    }, []);

    if (failed || balance === null) {
        return null;
    }

    return (
        <Link
            to={'/hyper/wallet'}
            className={className}
            css={tw`flex items-center gap-2 no-underline rounded-full px-3 py-1.5 text-sm font-semibold`}
            style={{ backgroundColor: 'rgb(var(--hyper-brand) / 0.14)', color: 'rgb(var(--hyper-brand-400))' }}
        >
            <FontAwesomeIcon icon={faCoins} />
            {balance.toLocaleString()}
        </Link>
    );
};
