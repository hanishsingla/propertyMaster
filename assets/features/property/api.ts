import { apiClient, buildQuery } from '@/lib/apiClient';
import type { Paginated, PropertyCard, PropertyDetail } from '@/lib/types';

export interface PropertyQuery {
  listingType?: string;
  type?: string;
  category?: string;
  city?: string;
  minPrice?: string | number;
  maxPrice?: string | number;
  bedRooms?: string | number;
  isFeatured?: string | number;
  q?: string;
  sort?: string;
  page?: number;
  perPage?: number;
}

export function fetchProperties(query: PropertyQuery, signal?: AbortSignal) {
  return apiClient.get<Paginated<PropertyCard>>(
    `/api/properties${buildQuery(query as Record<string, string | number | boolean | undefined | null>)}`,
    signal,
  );
}

export function fetchProperty(idOrSlug: string, signal?: AbortSignal) {
  return apiClient.get<PropertyDetail>(`/api/properties/${idOrSlug}`, signal);
}

export interface PropertyPayload {
  title: string;
  description: string;
  listingType: string;
  category: string;
  type: string;
  status?: string;
  price: number;
  area: number;
  areaUnit?: string;
  bedRooms?: number | null;
  bathRooms?: number | null;
  rooms?: number | null;
  direction?: string | null;
  city: string;
  state?: string | null;
  country?: string | null;
  latitude?: number | null;
  longitude?: number | null;
  isFeatured?: boolean;
}

export function createProperty(payload: PropertyPayload) {
  return apiClient.post<PropertyDetail>('/api/properties', payload);
}

export function updateProperty(id: number, payload: PropertyPayload) {
  return apiClient.patch<PropertyDetail>(`/api/properties/${id}`, payload);
}

export function deleteProperty(id: number) {
  return apiClient.delete<void>(`/api/properties/${id}`);
}

export function uploadImages(id: number, files: FileList | File[]) {
  const form = new FormData();
  Array.from(files).forEach((f) => form.append('images', f));
  return apiClient.postForm<PropertyDetail>(`/api/properties/${id}/images`, form);
}

export function updateImages(id: number, updates: { imageId: number; sortOrder?: number; isCover?: boolean }[]) {
  return apiClient.patch<PropertyDetail>(`/api/properties/${id}/images`, updates);
}

export function deleteImage(id: number, imageId: number) {
  return apiClient.delete<void>(`/api/properties/${id}/images/${imageId}`);
}
