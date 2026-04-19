import { createContext, ReactNode, useCallback, useContext, useState } from 'react';

type SidebarTheme = 'dark' | 'light';

const SIDEBAR_THEME_STORAGE_KEY = 'layout-1-sidebar-theme';

function readStoredSidebarTheme(): SidebarTheme {
  if (typeof window === 'undefined') {
    return 'dark';
  }
  try {
    const raw = window.localStorage.getItem(SIDEBAR_THEME_STORAGE_KEY);
    if (raw === 'dark' || raw === 'light') {
      return raw;
    }
  } catch {
    // storage indisponível (modo privado, quota, etc.)
  }
  return 'dark';
}

function persistSidebarTheme(theme: SidebarTheme) {
  try {
    window.localStorage.setItem(SIDEBAR_THEME_STORAGE_KEY, theme);
  } catch {
    // ignore
  }
}

// Define the shape of the layout state
interface LayoutState {
  sidebarCollapse: boolean;
  setSidebarCollapse: (open: boolean) => void;
  sidebarTheme: SidebarTheme;
  setSidebarTheme: (theme: SidebarTheme) => void;
}

// Create the context
const LayoutContext = createContext<LayoutState | undefined>(undefined);

// Provider component
interface LayoutProviderProps {
  children: ReactNode;
}

export function LayoutProvider({ children }: LayoutProviderProps) {
  const [sidebarCollapse, setSidebarCollapse] = useState(false);
  const [sidebarTheme, setSidebarThemeState] = useState<SidebarTheme>(() => readStoredSidebarTheme());

  const setSidebarTheme = useCallback((theme: SidebarTheme) => {
    setSidebarThemeState(theme);
    persistSidebarTheme(theme);
  }, []);

  return (
    <LayoutContext.Provider
      value={{
        sidebarCollapse,
        setSidebarCollapse,
        sidebarTheme,
        setSidebarTheme,
      }}
    >
      {children}
    </LayoutContext.Provider>
  );
}

// Custom hook for consuming the context
export const useLayout = () => {
  const context = useContext(LayoutContext);
  if (!context) {
    throw new Error('useLayout must be used within a LayoutProvider');
  }
  return context;
};
