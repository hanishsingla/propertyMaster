import { useState } from 'react';
import { Search, SlidersHorizontal } from 'lucide-react';
import { useEnums } from '@/lib/enums';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { Button } from '@/components/ui/button';

export interface PropertyFilters {
  q?: string;
  listingType?: string;
  category?: string;
  type?: string;
  city?: string;
  minPrice?: string;
  maxPrice?: string;
  bedRooms?: string;
  sort?: string;
}

export function FilterBar({
  value,
  onChange,
}: {
  value: PropertyFilters;
  onChange: (next: PropertyFilters) => void;
}) {
  const enums = useEnums();
  const [local, setLocal] = useState<PropertyFilters>(value);
  const [expanded, setExpanded] = useState(false);

  const set = (patch: Partial<PropertyFilters>) => setLocal((prev) => ({ ...prev, ...patch }));
  const apply = () => onChange(local);
  const reset = () => {
    const cleared: PropertyFilters = { sort: local.sort };
    setLocal(cleared);
    onChange(cleared);
  };

  return (
    <div className="rounded-lg border bg-card p-4 shadow-sm">
      <div className="flex flex-col gap-3 md:flex-row md:items-end">
        <div className="flex-1">
          <label className="mb-1 block text-xs font-medium text-muted-foreground">Search</label>
          <div className="relative">
            <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
            <Input
              className="pl-9"
              placeholder="Title, city, keyword…"
              value={local.q ?? ''}
              onChange={(e) => set({ q: e.target.value })}
              onKeyDown={(e) => e.key === 'Enter' && apply()}
            />
          </div>
        </div>
        <div className="w-full md:w-44">
          <label className="mb-1 block text-xs font-medium text-muted-foreground">Sort</label>
          <Select value={local.sort ?? 'newest'} onChange={(e) => onChange({ ...local, sort: e.target.value })}>
            <option value="newest">Newest</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
          </Select>
        </div>
        <Button variant="outline" onClick={() => setExpanded((e) => !e)} className="md:w-auto">
          <SlidersHorizontal className="h-4 w-4" /> Filters
        </Button>
        <Button onClick={apply}>Search</Button>
      </div>

      {expanded && (
        <div className="mt-4 grid grid-cols-1 gap-3 border-t pt-4 sm:grid-cols-2 lg:grid-cols-4">
          <Select value={local.listingType ?? ''} onChange={(e) => set({ listingType: e.target.value })}>
            <option value="">Any listing</option>
            {enums.listingType.map((o) => (
              <option key={o.value} value={o.value}>{o.label}</option>
            ))}
          </Select>
          <Select value={local.category ?? ''} onChange={(e) => set({ category: e.target.value })}>
            <option value="">Any category</option>
            {enums.propertyCategory.map((o) => (
              <option key={o.value} value={o.value}>{o.label}</option>
            ))}
          </Select>
          <Select value={local.type ?? ''} onChange={(e) => set({ type: e.target.value })}>
            <option value="">Any type</option>
            {enums.propertyType.map((o) => (
              <option key={o.value} value={o.value}>{o.label}</option>
            ))}
          </Select>
          <Input
            placeholder="City"
            value={local.city ?? ''}
            onChange={(e) => set({ city: e.target.value })}
          />
          <Input
            type="number"
            placeholder="Min price (₹)"
            value={local.minPrice ?? ''}
            onChange={(e) => set({ minPrice: e.target.value })}
          />
          <Input
            type="number"
            placeholder="Max price (₹)"
            value={local.maxPrice ?? ''}
            onChange={(e) => set({ maxPrice: e.target.value })}
          />
          <Select value={local.bedRooms ?? ''} onChange={(e) => set({ bedRooms: e.target.value })}>
            <option value="">Any beds</option>
            {[1, 2, 3, 4, 5].map((n) => (
              <option key={n} value={n}>{n}+ beds</option>
            ))}
          </Select>
          <div className="flex gap-2">
            <Button variant="outline" className="flex-1" onClick={reset}>
              Reset
            </Button>
            <Button className="flex-1" onClick={apply}>
              Apply
            </Button>
          </div>
        </div>
      )}
    </div>
  );
}
