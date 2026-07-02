import { ChevronLeft, ChevronRight } from 'lucide-react';
import { Button } from '@/components/ui/button';
import type { PaginationMeta } from '@/lib/types';

export function Pagination({
  meta,
  onPageChange,
}: {
  meta: PaginationMeta;
  onPageChange: (page: number) => void;
}) {
  if (meta.totalPages <= 1) return null;

  const pages: number[] = [];
  const start = Math.max(1, meta.page - 2);
  const end = Math.min(meta.totalPages, start + 4);
  for (let i = start; i <= end; i++) pages.push(i);

  return (
    <nav className="flex items-center justify-center gap-1" aria-label="Pagination">
      <Button
        variant="outline"
        size="icon"
        disabled={meta.page <= 1}
        onClick={() => onPageChange(meta.page - 1)}
        aria-label="Previous page"
      >
        <ChevronLeft className="h-4 w-4" />
      </Button>
      {start > 1 && <span className="px-2 text-muted-foreground">…</span>}
      {pages.map((p) => (
        <Button
          key={p}
          variant={p === meta.page ? 'default' : 'outline'}
          size="icon"
          onClick={() => onPageChange(p)}
          aria-current={p === meta.page ? 'page' : undefined}
        >
          {p}
        </Button>
      ))}
      {end < meta.totalPages && <span className="px-2 text-muted-foreground">…</span>}
      <Button
        variant="outline"
        size="icon"
        disabled={meta.page >= meta.totalPages}
        onClick={() => onPageChange(meta.page + 1)}
        aria-label="Next page"
      >
        <ChevronRight className="h-4 w-4" />
      </Button>
    </nav>
  );
}
