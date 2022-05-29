import {createContext, ReactNode, useContext, useState} from 'react';
import type User from '../types/User';

interface UserStore {
    user: User;
    setUser: (user: User | ((prevUser: User) => User)) => void;
}

const Store = createContext<UserStore>(null);

export function UserContextProvider({children}: {children: ReactNode}) {
    const [user, setUser] = useState<User>(window.sdrtVolunteerPortal.user);
    return <Store.Provider value={{user, setUser}}>{children}</Store.Provider>;
}

export function useUserContext(): UserStore {
    return useContext<UserStore>(Store);
}

export default Store;
