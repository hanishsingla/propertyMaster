import * as React from 'react';
import { useQuery, useQueryClient } from '@tanstack/react-query';
import { apiClient, refreshCsrf } from '@/lib/apiClient';
import { queryKeys } from '@/lib/query';
import type { UserSelf } from '@/lib/types';

interface MeResponse {
  user: UserSelf | null;
}

interface AuthContextValue {
  user: UserSelf | null;
  isLoading: boolean;
  isAuthenticated: boolean;
  isAgent: boolean;
  /** Call after login/register/logout to re-hydrate session + CSRF. */
  refresh: () => Promise<void>;
}

const AuthContext = React.createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const queryClient = useQueryClient();
  const { data, isLoading } = useQuery({
    queryKey: queryKeys.me,
    queryFn: () => apiClient.get<MeResponse>('/api/me'),
    staleTime: 60_000,
  });

  const user = data?.user ?? null;

  const refresh = React.useCallback(async () => {
    await refreshCsrf();
    await queryClient.invalidateQueries({ queryKey: queryKeys.me });
    await queryClient.refetchQueries({ queryKey: queryKeys.me });
  }, [queryClient]);

  const value = React.useMemo<AuthContextValue>(
    () => ({
      user,
      isLoading,
      isAuthenticated: !!user,
      isAgent: !!user?.isAgent,
      refresh,
    }),
    [user, isLoading, refresh],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth(): AuthContextValue {
  const ctx = React.useContext(AuthContext);
  if (!ctx) throw new Error('useAuth must be used within AuthProvider');
  return ctx;
}
