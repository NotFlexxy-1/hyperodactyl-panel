import * as React from 'react';
import { useState } from 'react';
import { Link, NavLink } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faBars, faBox, faCogs, faLayerGroup, faSignOutAlt, faStore, faTimes, faTrophy } from '@fortawesome/free-solid-svg-icons';
import { useStoreState } from 'easy-peasy';
import { ApplicationStore } from '@/state';
import SearchContainer from '@/components/dashboard/search/SearchContainer';
import tw from 'twin.macro';
import styled from 'styled-components/macro';
import http from '@/api/http';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import Tooltip from '@/components/elements/tooltip/Tooltip';
import Avatar from '@/components/Avatar';
import CoinBalance from '@/components/hyper/CoinBalance';

const RightNavigation = styled.div`
    & > a,
    & > button,
    & > .navigation-link {
        ${tw`flex items-center h-full no-underline px-4 sm:px-5 cursor-pointer transition-all duration-150 rounded-lg`};
        color: rgb(var(--hyper-text-muted));

        &:active,
        &:hover {
            color: rgb(var(--hyper-text));
            background-color: rgb(var(--hyper-brand) / 0.1);
        }

        &.active {
            color: rgb(var(--hyper-text));
            box-shadow: inset 0 -2px rgb(var(--hyper-brand));
        }
    }
`;

const MobileLink = styled(NavLink)`
    ${tw`flex items-center gap-3 px-4 py-3 rounded-xl no-underline font-medium transition-colors`};
    color: rgb(var(--hyper-text-muted));

    &:hover,
    &.active {
        color: rgb(var(--hyper-text));
        background-color: rgb(var(--hyper-brand) / 0.1);
    }
`;

export default () => {
    const name = useStoreState((state: ApplicationStore) => state.settings.data!.name);
    const rootAdmin = useStoreState((state: ApplicationStore) => state.user.data!.rootAdmin);
    const [isLoggingOut, setIsLoggingOut] = useState(false);
    const [drawerOpen, setDrawerOpen] = useState(false);

    const onTriggerLogout = () => {
        setIsLoggingOut(true);
        http.post('/auth/logout').finally(() => {
            // @ts-expect-error this is valid
            window.location = '/';
        });
    };

    return (
        <div
            className={'w-full sticky top-0 z-40 overflow-x-visible'}
            style={{
                backgroundColor: 'rgb(var(--hyper-surface-1) / 0.85)',
                backdropFilter: 'blur(14px) saturate(140%)',
                borderBottom: '1px solid rgb(var(--hyper-border))',
            }}
        >
            <SpinnerOverlay visible={isLoggingOut} />
            <div className={'mx-auto w-full flex items-center h-14 max-w-[1200px] px-2 sm:px-4'}>
                <button
                    className={'sm:hidden mr-1 p-2 rounded-lg'}
                    style={{ color: 'rgb(var(--hyper-text-muted))' }}
                    onClick={() => setDrawerOpen(true)}
                >
                    <FontAwesomeIcon icon={faBars} />
                </button>
                <div id={'logo'} className={'flex-1 min-w-0'}>
                    <Link
                        to={'/'}
                        className={'text-lg sm:text-2xl font-header font-semibold px-2 sm:px-4 no-underline transition-colors duration-150 truncate inline-block max-w-full'}
                        style={{ color: 'rgb(var(--hyper-text))' }}
                    >
                        {name}
                    </Link>
                </div>
                <RightNavigation className={'hidden sm:flex h-full items-center justify-center'}>
                    <CoinBalance />
                    <SearchContainer />
                    <Tooltip placement={'bottom'} content={'Dashboard'}>
                        <NavLink to={'/'} exact>
                            <FontAwesomeIcon icon={faLayerGroup} />
                        </NavLink>
                    </Tooltip>
                    <Tooltip placement={'bottom'} content={'LXC Containers'}>
                        <NavLink to={'/lxc'}>
                            <FontAwesomeIcon icon={faBox} />
                        </NavLink>
                    </Tooltip>
                    <Tooltip placement={'bottom'} content={'Hyper Coin Store'}>
                        <NavLink to={'/hyper/store'}>
                            <FontAwesomeIcon icon={faStore} />
                        </NavLink>
                    </Tooltip>
                    <Tooltip placement={'bottom'} content={'Achievements'}>
                        <NavLink to={'/hyper/achievements'}>
                            <FontAwesomeIcon icon={faTrophy} />
                        </NavLink>
                    </Tooltip>
                    {rootAdmin && (
                        <Tooltip placement={'bottom'} content={'Admin'}>
                            <a href={'/admin'} rel={'noreferrer'}>
                                <FontAwesomeIcon icon={faCogs} />
                            </a>
                        </Tooltip>
                    )}
                    <Tooltip placement={'bottom'} content={'Account Settings'}>
                        <NavLink to={'/account'}>
                            <span className={'flex items-center w-5 h-5'}>
                                <Avatar.User />
                            </span>
                        </NavLink>
                    </Tooltip>
                    <Tooltip placement={'bottom'} content={'Sign Out'}>
                        <button onClick={onTriggerLogout}>
                            <FontAwesomeIcon icon={faSignOutAlt} />
                        </button>
                    </Tooltip>
                </RightNavigation>
                <div className={'flex sm:hidden items-center gap-1'}>
                    <SearchContainer />
                    <NavLink to={'/account'} className={'flex items-center w-7 h-7 rounded-full overflow-hidden'}>
                        <Avatar.User />
                    </NavLink>
                </div>
            </div>

            {/* Mobile drawer */}
            {drawerOpen && (
                <div className={'fixed inset-0 z-50 sm:hidden'}>
                    <div
                        className={'absolute inset-0 animate-fade-in'}
                        style={{ backgroundColor: 'rgb(0 0 0 / 0.6)' }}
                        onClick={() => setDrawerOpen(false)}
                    />
                    <div
                        className={'absolute left-0 top-0 h-full w-72 max-w-[80%] p-4 animate-slide-in-right flex flex-col gap-1'}
                        style={{ backgroundColor: 'rgb(var(--hyper-surface-1))', borderRight: '1px solid rgb(var(--hyper-border))' }}
                    >
                        <div className={'flex items-center justify-between mb-4'}>
                            <span className={'font-header font-semibold text-lg'} style={{ color: 'rgb(var(--hyper-text))' }}>
                                {name}
                            </span>
                            <button onClick={() => setDrawerOpen(false)} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                                <FontAwesomeIcon icon={faTimes} />
                            </button>
                        </div>
                        <MobileLink to={'/'} exact onClick={() => setDrawerOpen(false)}>
                            <FontAwesomeIcon icon={faLayerGroup} /> Dashboard
                        </MobileLink>
                        {rootAdmin && (
                            <a
                                href={'/admin'}
                                rel={'noreferrer'}
                                className={'flex items-center gap-3 px-4 py-3 rounded-xl no-underline font-medium'}
                                style={{ color: 'rgb(var(--hyper-text-muted))' }}
                            >
                                <FontAwesomeIcon icon={faCogs} /> Admin
                            </a>
                        )}
                        <MobileLink to={'/lxc'} onClick={() => setDrawerOpen(false)}>
                            <FontAwesomeIcon icon={faBox} /> LXC Containers
                        </MobileLink>
                        <MobileLink to={'/hyper/store'} onClick={() => setDrawerOpen(false)}>
                            <FontAwesomeIcon icon={faStore} /> Coin Store
                        </MobileLink>
                        <MobileLink to={'/hyper/wallet'} onClick={() => setDrawerOpen(false)}>
                            <FontAwesomeIcon icon={faTrophy} /> Wallet
                        </MobileLink>
                        <MobileLink to={'/account'} onClick={() => setDrawerOpen(false)}>
                            <FontAwesomeIcon icon={faSignOutAlt} /> Account Settings
                        </MobileLink>
                        <button
                            onClick={onTriggerLogout}
                            className={'flex items-center gap-3 px-4 py-3 rounded-xl font-medium mt-auto'}
                            style={{ color: 'rgb(var(--hyper-danger))' }}
                        >
                            <FontAwesomeIcon icon={faSignOutAlt} /> Sign Out
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
};
