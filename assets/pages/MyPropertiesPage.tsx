import { useState } from 'react';
import { useQuery, useQueryClient, keepPreviousData } from '@tanstack/react-query';
import { Link } from '@tanstack/react-router';
import { Plus, Pencil, Trash2, Building2, ImageIcon } from 'lucide-react';
import { apiClient, buildQuery, ApiError } from '@/lib/apiClient';
import { deleteProperty } from '@/features/property/api';
import type { Paginated, PropertyCard } from '@/lib/types';
import { RequireAuth } from '@/components/RequireAuth';
import { EmptyState } from '@/components/EmptyState';
import { Pagination } from '@/components/Pagination';
import { Skeleton } from '@/components/Skeletons';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog } from '@/components/ui/dialog';
import { useToast } from '@/components/ui/toast';

function MyPropertiesInner() {
  const toast = useToast();
  const queryClient = useQueryClient();
  const [page, setPage] = useState(1);
  const [toDelete, setToDelete] = useState<PropertyCard | null>(null);
  const [busy, setBusy] = useState(false);

  const { data, isLoading } = useQuery({
    queryKey: ['my-properties', { page }],
    queryFn: ({ signal }) =>
      apiClient.get<Paginated<PropertyCard>>(`/api/my/properties${buildQuery({ page, perPage: 12 })}`, signal),
    placeholderData: keepPreviousData,
  });

  const confirmDelete = async () => {
    if (!toDelete) return;
    setBusy(true);
    try {
      await deleteProperty(toDelete.id);
      queryClient.invalidateQueries({ queryKey: ['my-properties'] });
      toast.success('Property deleted');
    } catch (err) {
      toast.error(err instanceof ApiError ? err.message : 'Could not delete property');
    } finally {
      setBusy(false);
      setToDelete(null);
    }
  };

  return (
    <div className="container py-8">
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">My Properties</h1>
          <p className="mt-1 text-muted-foreground">Manage your listings.</p>
        </div>
        <Link to="/my/properties/new">
          <Button>
            <Plus className="h-4 w-4" /> New listing
          </Button>
        </Link>
      </div>

      {isLoading ? (
        <div className="space-y-3">
          {Array.from({ length: 4 }).map((_, i) => (
            <Skeleton key={i} className="h-24 w-full" />
          ))}
        </div>
      ) : !data || data.data.length === 0 ? (
        <EmptyState
          icon={Building2}
          title="No listings yet"
          description="Create your first property listing to get started."
          action={
            <Link to="/my/properties/new">
              <Button>
                <Plus className="h-4 w-4" /> New listing
              </Button>
            </Link>
          }
        />
      ) : (
        <>
          <div className="space-y-3">
            {data.data.map((p) => (
              <div key={p.id} className="flex items-center gap-4 rounded-lg border bg-card p-3">
                <div className="h-16 w-24 flex-none overflow-hidden rounded-md bg-muted">
                  {p.coverImageUrl ? (
                    <img src={p.coverImageUrl} alt="" className="h-full w-full object-cover" />
                  ) : (
                    <div className="flex h-full w-full items-center justify-center text-muted-foreground">
                      <ImageIcon className="h-5 w-5" />
                    </div>
                  )}
                </div>
                <div className="min-w-0 flex-1">
                  <div className="flex items-center gap-2">
                    <h3 className="truncate font-semibold">{p.title}</h3>
                    <Badge variant="muted" className="capitalize">{p.listingType}</Badge>
                    {p.isFeatured && <Badge variant="secondary">Featured</Badge>}
                  </div>
                  <p className="text-sm text-muted-foreground">
                    {p.priceFormatted} · {p.city ?? '—'}
                  </p>
                </div>
                <div className="flex flex-none gap-2">
                  <Link to="/properties/$slug" params={{ slug: p.slug }}>
                    <Button variant="ghost" size="sm">View</Button>
                  </Link>
                  <Link to="/my/properties/$id/edit" params={{ id: String(p.id) }}>
                    <Button variant="outline" size="sm">
                      <Pencil className="h-4 w-4" /> Edit
                    </Button>
                  </Link>
                  <Button variant="ghost" size="icon" onClick={() => setToDelete(p)} aria-label="Delete">
                    <Trash2 className="h-4 w-4 text-destructive" />
                  </Button>
                </div>
              </div>
            ))}
          </div>
          <div className="mt-8">
            <Pagination meta={data.meta} onPageChange={setPage} />
          </div>
        </>
      )}

      <Dialog
        open={!!toDelete}
        onClose={() => setToDelete(null)}
        title="Delete property?"
        description={toDelete ? `"${toDelete.title}" will be permanently removed.` : ''}
      >
        <div className="flex justify-end gap-2">
          <Button variant="outline" onClick={() => setToDelete(null)}>Cancel</Button>
          <Button variant="destructive" loading={busy} onClick={confirmDelete}>Delete</Button>
        </div>
      </Dialog>
    </div>
  );
}

export function MyPropertiesPage() {
  return (
    <RequireAuth requireAgent>
      <MyPropertiesInner />
    </RequireAuth>
  );
}
