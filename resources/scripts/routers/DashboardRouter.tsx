import React from 'react';
import { NavLink, Route, Switch } from 'react-router-dom';
import NavigationBar from '@/components/NavigationBar';
import DashboardContainer from '@/components/dashboard/DashboardContainer';
import { NotFound } from '@/components/elements/ScreenBlock';
import TransitionRouter from '@/TransitionRouter';
import SubNavigation from '@/components/elements/SubNavigation';
import { useLocation } from 'react-router';
import Spinner from '@/components/elements/Spinner';
import routes from '@/routers/routes';
import LxcListContainer from '@/components/lxc/LxcListContainer';
import LxcContainerRouter from '@/components/lxc/LxcContainerRouter';
import WalletContainer from '@/components/hyper/WalletContainer';
import StoreContainer from '@/components/hyper/StoreContainer';
import AchievementsContainer from '@/components/hyper/AchievementsContainer';

export default () => {
    const location = useLocation();

    return (
        <>
            <NavigationBar />
            {location.pathname.startsWith('/account') && (
                <SubNavigation>
                    <div>
                        {routes.account
                            .filter((route) => !!route.name)
                            .map(({ path, name, exact = false }) => (
                                <NavLink key={path} to={`/account/${path}`.replace('//', '/')} exact={exact}>
                                    {name}
                                </NavLink>
                            ))}
                    </div>
                </SubNavigation>
            )}
            <TransitionRouter>
                <React.Suspense fallback={<Spinner centered />}>
                    <Switch location={location}>
                        <Route path={'/'} exact>
                            <DashboardContainer />
                        </Route>
                        {routes.account.map(({ path, component: Component }) => (
                            <Route key={path} path={`/account/${path}`.replace('//', '/')} exact>
                                <Component />
                            </Route>
                        ))}
                        <Route path={'/lxc'} exact>
                            <LxcListContainer />
                        </Route>
                        <Route path={'/lxc/:id'}>
                            <LxcContainerRouter />
                        </Route>
                        <Route path={'/hyper/wallet'} exact>
                            <WalletContainer />
                        </Route>
                        <Route path={'/hyper/store'} exact>
                            <StoreContainer />
                        </Route>
                        <Route path={'/hyper/achievements'} exact>
                            <AchievementsContainer />
                        </Route>
                        <Route path={'*'}>
                            <NotFound />
                        </Route>
                    </Switch>
                </React.Suspense>
            </TransitionRouter>
        </>
    );
};
