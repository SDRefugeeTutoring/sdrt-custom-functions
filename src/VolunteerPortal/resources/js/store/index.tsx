import {createContext, ReactNode, useContext} from 'react';
import type User from '../types/User';

interface Store {
    user: User;
}

const Store = createContext<Store>({
    user: window.sdrtVolunteerPortal.user,
});

export function StoreProvider({children}: {children: ReactNode}) {
    return <Store.Provider value={{user: window.sdrtVolunteerPortal.user}}>{children}</Store.Provider>;
}

export function useStore(): Store {
    return useContext<Store>(Store);
}

export default Store;
