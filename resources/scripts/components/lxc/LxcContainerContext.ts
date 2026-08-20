import React, { useContext } from 'react';
import { LxcContainer } from '@/api/lxc/types';

interface LxcContextValue {
    container: LxcContainer;
    setContainer: (container: LxcContainer) => void;
    refresh: () => void;
}

export const LxcContainerContext = React.createContext<LxcContextValue | null>(null);

export const useLxcContainer = (): LxcContextValue => {
    const value = useContext(LxcContainerContext);

    if (!value) {
        throw new Error('useLxcContainer must be used within an LxcContainerRouter.');
    }

    return value;
};
