import { useMemo, useState } from 'react';
import { useQuery, keepPreviousData } from '@tanstack/react-query';
import { useSearch } from '@tanstack/react-router';
import { LayoutGrid, Map as MapIcon, Home } from 'lucide-react';
import { fetchProperties } from '@/features/property/api';
import { FilterBar, type PropertyFilters } from '@/components/FilterBar';
import { PropertyCard } from '@/components/PropertyCard';
import { PropertyGridSkeleton } from '@/components/Skeletons';
import { Pagination } from '@/components/Pagination';
import { EmptyState } from '@/components/EmptyState';
import { PropertyMap, type MapPoint } from '@/components/PropertyMap';
import { cn } from '@/lib/utils';

interface Props {
  fixedListingType?: string;
  title: string;
  subtitle?: string;
  allowMap?: boolean;
}

export function ListingPage({ fixedListingType, title, subtitle, allowMap = true }: Props) {
  const search = useSearch({ strict: false }) as Record<string, string>;
  const [filters, setFilters] = useState<PropertyFilters>({
    q: search.q ?? '',
    sort: 'newest',
  });
  const [page, setPage] = useState(1);
  const [view, setView] = useState<'grid' | 'map'>('grid');

  const query = useMemo(
    () => ({
      ...filters,
      listingType: fixedListingType ?? filters.listingType,
      page,
      perPage: 12,
    }),
    [filters, fixedListingType, page],
  );

  const { data, isLoading, isFetching } = useQuery({
    queryKey: ['properties', query],
    queryFn: ({ signal }) => fetchProperties(query, signal),
    placeholderData: keepPreviousData,
  });

  const items = data?.data ?? [];
  const points: MapPoint[] = items
    .filter((p) => 'latitude' in p)
    .map((p) => ({ id: p.id, lat: (p as any).latitude, lng: (p as any).longitude, label: p.title }));

  const handleFilters = (next: PropertyFilters) => {
    setFilters(next);
    setPage(1);
  };

  return (
    <div className="container py-8">
      <div className="mb-6">
        <h1 className="text-3xl font-bold">{title}</h1>
        {subtitle && <p className="mt-1 text-muted-foreground">{subtitle}</p>}
      </div>

      {!fixedListingType && (
        <div className="mb-6">
          <FilterBar value={filters} onChange={handleFilters} />
        </div>
      )}

      <div className="mb-4 flex items-center justify-between">
        <p className="text-sm text-muted-foreground">
          {data ? `${data.meta.total} propert${data.meta.total === 1 ? 'y' : 'ies'} found` : '…'}
        </p>
        {allowMap && (
          <div className="flex overflow-hidden rounded-md border">
            <button
              onClick={() => setView('grid')}
              className={cn('flex items-center gap-1 px-3 py-1.5 text-sm', view === 'grid' && 'bg-secondary')}
            >
              <LayoutGrid className="h-4 w-4" /> Grid
            </button>
            <button
              onClick={() => setView('map')}
              className={cn('flex items-center gap-1 px-3 py-1.5 text-sm', view === 'map' && 'bg-secondary')}
            >
              <MapIcon className="h-4 w-4" /> Map
            </button>
          </div>
        )}
      </div>

      {isLoading ? (
        <PropertyGridSkeleton count={9} />
      ) : items.length === 0 ? (
        <EmptyState
          icon={Home}
          title="No properties found"
          description="Try adjusting your filters or search terms."
        />
      ) : view === 'map' ? (
        <div className="h-[600px] overflow-hidden rounded-lg border">
          <PropertyMap points={points} />
        </div>
      ) : (
        <div className={cn('transition-opacity', isFetching && 'opacity-60')}>
          <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {items.map((p) => (
              <PropertyCard key={p.id} property={p} />
            ))}
          </div>
        </div>
      )}

      {data && data.meta.totalPages > 1 && view === 'grid' && (
        <div className="mt-8">
          <Pagination meta={data.meta} onPageChange={setPage} />
        </div>
      )}
    </div>
  );
}
