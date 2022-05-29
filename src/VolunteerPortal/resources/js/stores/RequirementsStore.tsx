import {createContext, ReactNode, useContext, useState} from 'react';
import {BackgroundCheck} from '../types/Requirements';

interface RequirementsStore {
    backgroundCheck: BackgroundCheck;
    setBackgroundCheck: (
        backgroundCheck: BackgroundCheck | ((prevBackgroundCheck: BackgroundCheck) => BackgroundCheck)
    ) => void;
}

const Store = createContext<RequirementsStore>(null);

export function RequirementsContextProvider({children}: {children: ReactNode}) {
    const [backgroundCheck, setBackgroundCheck] = useState<BackgroundCheck>({
        status: window.sdrtVolunteerPortal.requirements.backgroundCheck.status,
        invitationUrl: window.sdrtVolunteerPortal.requirements.backgroundCheck.inviteUrl,
    });
    return <Store.Provider value={{backgroundCheck, setBackgroundCheck}}>{children}</Store.Provider>;
}

export function useRequirementsContext(): RequirementsStore {
    return useContext<RequirementsStore>(Store);
}

export default Store;
