import React, { useCallback, useEffect, useState } from 'react';
import { NavLink, Route, Switch, useRouteMatch } from 'react-router-dom';
import tw from 'twin.macro';
import SubNavigation from '@/components/elements/SubNavigation';
import Spinner from '@/components/elements/Spinner';
import ErrorState from '@/components/elements/ErrorState';
import getLxcContainer from '@/api/lxc/getLxcContainer';
import { LxcContainer } from '@/api/lxc/types';
import { LxcContainerContext } from '@/components/lxc/LxcContainerContext';
import LxcOverviewContainer from '@/components/lxc/LxcOverviewContainer';
import LxcConsoleContainer from '@/components/lxc/LxcConsoleContainer';
import LxcNetworkContainer from '@/components/lxc/LxcNetworkContainer';
import LxcSnapshotsContainer from '@/components/lxc/LxcSnapshotsContainer';
import LxcSettingsContainer from '@/components/lxc/LxcSettingsContainer';
import { NotFound } from '@/components/elements/ScreenBlock';

export default () => {
    const match = useRouteMatch<{ id: string }>();
    const uuid = match.params.id;

    const [container, setContainer] = useState<LxcContainer | null>(null);
    const [error, setError] = useState<string | null>(null);

    const refresh = useCallback(() => {
        setError(null);
        getLxcContainer(uuid)
            .then(setContainer)
            .catch((e) => setError(e?.message || 'Unable to load this container.'));
    }, [uuid]);

    useEffect(() => {
        refresh();
    }, [refresh]);

    if (error) {
        return <ErrorState message={error} onRetry={refresh} />;
    }

    if (!container) {
        return <Spinner centered size={'large'} />;
    }

    return (
        <LxcContainerContext.Provider value={{ container, setContainer, refresh }}>
            <SubNavigation>
                <div>
                    <NavLink to={`${match.url}`} exact>
                        Overview
                    </NavLink>
                    <NavLink to={`${match.url}/console`}>Console</NavLink>
                    <NavLink to={`${match.url}/network`}>Network</NavLink>
                    <NavLink to={`${match.url}/snapshots`}>Snapshots</NavLink>
                    <NavLink to={`${match.url}/settings`}>Settings</NavLink>
                </div>
            </SubNavigation>
            <div css={tw`w-full`}>
                <Switch>
                    <Route path={`${match.path}`} exact>
                        <LxcOverviewContainer />
                    </Route>
                    <Route path={`${match.path}/console`} exact>
                        <LxcConsoleContainer />
                    </Route>
                    <Route path={`${match.path}/network`} exact>
                        <LxcNetworkContainer />
                    </Route>
                    <Route path={`${match.path}/snapshots`} exact>
                        <LxcSnapshotsContainer />
                    </Route>
                    <Route path={`${match.path}/settings`} exact>
                        <LxcSettingsContainer />
                    </Route>
                    <Route path={'*'}>
                        <NotFound />
                    </Route>
                </Switch>
            </div>
        </LxcContainerContext.Provider>
    );
};
