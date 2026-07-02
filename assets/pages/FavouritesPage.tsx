import { useQuery } from '@tanstack/react-query';
import { Link } from '@tanstack/react-router';
import { Heart } from 'lucide-react';
import { apiClient } from '@/lib/apiClient';
import type { PropertyCard as PropertyCardType } from '@/lib/types';
import { RequireAuth } from '@/components/RequireAuth';
import { PropertyCard } from '@/components/PropertyCard';
import { PropertyGridSkeleton } from '@/components/Skeletons';
import { EmptyState } from '@/components/EmptyState';
import { Button } from '@/components/ui/button';

function FavouritesInner() {
  const { data, isLoading } = useQuery({
    queryKey: ['favourites'],
    queryFn: ({ signal }) => apiClient.get<{ data: PropertyCardType[] }>('/api/favourites', signal),
  });

  return (
    <div className="container py-8">
      <div className="mb-6">
        <h1 className="text-3xl font-bold">Saved Properties</h1>
        <p className="mt-1 text-muted-foreground">Properties you've added to your favourites.</p>
      </div>

      {isLoading ? (
        <PropertyGridSkeleton count={6} />
      ) : !data || data.data.length === 0 ? (
        <EmptyState
          icon={Heart}
          title="No saved properties yet"
          description="Tap the heart on any listing to save it here."
          action={
            <Link to="/properties">
              <Button>Browse properties</Button>
            </Link>
          }
        />
      ) : (
        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {data.data.map((p) => (
            <PropertyCard key={p.id} property={p} />
          ))}
        </div>
      )}
    </div>
  );
}

export function FavouritesPage() {
  return (
    <RequireAuth>
      <FavouritesInner />
    </RequireAuth>
  );
}
