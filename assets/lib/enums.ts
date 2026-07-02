import { useQuery } from '@tanstack/react-query';
import { apiClient } from './apiClient';
import { queryKeys } from './query';
import type { Enums } from './types';

const EMPTY: Enums = {
  gender: [],
  listingType: [],
  propertyType: [],
  propertyCategory: [],
  propertyStatus: [],
  areaUnit: [],
  direction: [],
};

export function useEnums(): Enums {
  const { data } = useQuery({
    queryKey: queryKeys.enums,
    queryFn: () => apiClient.get<Enums>('/api/enums'),
    staleTime: Infinity,
  });
  return data ?? EMPTY;
}

export function labelFor(options: { value: string; label: string }[], value: string | null | undefined): string {
  if (!value) return '';
  return options.find((o) => o.value === value)?.label ?? value;
}
