import { QueryClient } from '@tanstack/react-query';
import { ApiError } from './apiClient';

export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 30_000,
      retry: (failureCount, error) => {
        // Never retry auth/client errors.
        if (error instanceof ApiError && error.status >= 400 && error.status < 500) {
          return false;
        }
        return failureCount < 2;
      },
      refetchOnWindowFocus: false,
    },
  },
});

export const queryKeys = {
  me: ['me'] as const,
  enums: ['enums'] as const,
  properties: (filters: unknown) => ['properties', filters] as const,
  property: (idOrSlug: string) => ['property', idOrSlug] as const,
  myProperties: (filters: unknown) => ['my-properties', filters] as const,
  favourites: ['favourites'] as const,
  agents: (filters: unknown) => ['agents', filters] as const,
  agent: (id: string) => ['agent', id] as const,
  account: ['account'] as const,
};
