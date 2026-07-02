import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { useNavigate, Link } from '@tanstack/react-router';
import { Search, Building2, KeyRound, TrendingUp } from 'lucide-react';
import { fetchProperties } from '@/features/property/api';
import { PropertyCard } from '@/components/PropertyCard';
import { PropertyGridSkeleton } from '@/components/Skeletons';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { EmptyState } from '@/components/EmptyState';

export function HomePage() {
  const navigate = useNavigate();
  const [q, setQ] = useState('');

  const featured = useQuery({
    queryKey: ['properties', { isFeatured: 1, perPage: 6 }],
    queryFn: ({ signal }) => fetchProperties({ isFeatured: 1, perPage: 6 }, signal),
  });
  const recent = useQuery({
    queryKey: ['properties', { sort: 'newest', perPage: 6 }],
    queryFn: ({ signal }) => fetchProperties({ sort: 'newest', perPage: 6 }, signal),
  });

  const search = () => {
    navigate({ to: '/properties', search: q ? { q } : {} });
  };

  return (
    <div>
      {/* Hero */}
      <section className="relative overflow-hidden bg-gradient-to-br from-primary via-primary to-indigo-700 text-primary-foreground">
        <div className="container py-20 md:py-28">
          <div className="max-w-2xl">
            <h1 className="text-4xl font-bold leading-tight md:text-5xl">
              Find a place you'll love to call home
            </h1>
            <p className="mt-4 text-lg text-primary-foreground/80">
              Browse thousands of properties for sale and rent from trusted agents.
            </p>
            <div className="mt-8 flex flex-col gap-3 rounded-xl bg-white p-2 shadow-lg sm:flex-row">
              <div className="relative flex-1">
                <Search className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                <Input
                  className="h-12 border-0 pl-10 text-foreground focus-visible:ring-0"
                  placeholder="Search by city, title or keyword…"
                  value={q}
                  onChange={(e) => setQ(e.target.value)}
                  onKeyDown={(e) => e.key === 'Enter' && search()}
                />
              </div>
              <Button size="lg" className="h-12" onClick={search}>
                Search
              </Button>
            </div>
            <div className="mt-4 flex gap-3">
              <Link to="/buy" className="rounded-full bg-white/15 px-4 py-1.5 text-sm font-medium hover:bg-white/25">
                Buy
              </Link>
              <Link to="/rent" className="rounded-full bg-white/15 px-4 py-1.5 text-sm font-medium hover:bg-white/25">
                Rent
              </Link>
              <Link to="/agents" className="rounded-full bg-white/15 px-4 py-1.5 text-sm font-medium hover:bg-white/25">
                Find an agent
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Quick categories */}
      <section className="container -mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <QuickCard icon={Building2} title="Buy a home" text="Find your dream property" to="/buy" />
        <QuickCard icon={KeyRound} title="Rent a home" text="Flexible rental listings" to="/rent" />
        <QuickCard icon={TrendingUp} title="Featured" text="Hand-picked properties" to="/properties" />
      </section>

      {/* Featured */}
      <Section title="Featured properties" href="/properties" linkLabel="View all">
        {featured.isLoading ? (
          <PropertyGridSkeleton count={3} />
        ) : featured.data && featured.data.data.length > 0 ? (
          <Grid items={featured.data.data} />
        ) : (
          <EmptyState title="No featured properties yet" description="Check back soon." />
        )}
      </Section>

      {/* Recent */}
      <Section title="Recent listings" href="/properties" linkLabel="Browse all">
        {recent.isLoading ? (
          <PropertyGridSkeleton count={3} />
        ) : recent.data && recent.data.data.length > 0 ? (
          <Grid items={recent.data.data} />
        ) : (
          <EmptyState title="No listings yet" />
        )}
      </Section>
    </div>
  );
}

function QuickCard({
  icon: Icon,
  title,
  text,
  to,
}: {
  icon: typeof Building2;
  title: string;
  text: string;
  to: string;
}) {
  return (
    <Link
      to={to}
      className="flex items-center gap-4 rounded-xl border bg-card p-5 shadow-sm transition-shadow hover:shadow-md"
    >
      <span className="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10 text-primary">
        <Icon className="h-6 w-6" />
      </span>
      <div>
        <div className="font-semibold">{title}</div>
        <div className="text-sm text-muted-foreground">{text}</div>
      </div>
    </Link>
  );
}

function Section({
  title,
  href,
  linkLabel,
  children,
}: {
  title: string;
  href: string;
  linkLabel: string;
  children: React.ReactNode;
}) {
  return (
    <section className="container py-12">
      <div className="mb-6 flex items-center justify-between">
        <h2 className="text-2xl font-bold">{title}</h2>
        <Link to={href} className="text-sm font-medium text-primary hover:underline">
          {linkLabel}
        </Link>
      </div>
      {children}
    </section>
  );
}

function Grid({ items }: { items: import('@/lib/types').PropertyCard[] }) {
  return (
    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      {items.map((p) => (
        <PropertyCard key={p.id} property={p} />
      ))}
    </div>
  );
}
