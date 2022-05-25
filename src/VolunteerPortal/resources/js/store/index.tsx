import {createContext, ReactNode, useContext, useState} from 'react';
import type User from '../types/User';

interface Store {
    user: User;
    setUser: (user: User | ((prevUser: User) => User)) => void;
}

const Store = createContext<Store>(null);

export function UserContextProvider({children}: {children: ReactNode}) {
    const [user, setUser] = useState<User>(window.sdrtVolunteerPortal.user);
    return <Store.Provider value={{user, setUser}}>{children}</Store.Provider>;
}

export function useUserContext(): Store {
    return useContext<Store>(Store);
}

export default Store;
