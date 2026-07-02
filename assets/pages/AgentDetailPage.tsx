import { useQuery } from '@tanstack/react-query';
import { useParams, Link } from '@tanstack/react-router';
import { Phone, Smartphone, MapPin, Users } from 'lucide-react';
import { apiClient } from '@/lib/apiClient';
import type { Agent, PropertyCard as PropertyCardType } from '@/lib/types';
import { FullSpinner } from '@/components/ui/spinner';
import { Card, CardContent } from '@/components/ui/card';
import { PropertyCard } from '@/components/PropertyCard';
import { EmptyState } from '@/components/EmptyState';

type AgentDetail = Agent & { properties: PropertyCardType[] };

export function AgentDetailPage() {
  const { id } = useParams({ strict: false }) as { id: string };
  const { data, isLoading, isError } = useQuery({
    queryKey: ['agent', id],
    queryFn: ({ signal }) => apiClient.get<AgentDetail>(`/api/agents/${id}`, signal),
  });

  if (isLoading) return <FullSpinner />;
  if (isError || !data) {
    return (
      <div className="container py-12">
        <EmptyState icon={Users} title="Agent not found" />
      </div>
    );
  }

  return (
    <div className="container py-8">
      <Card className="mb-8">
        <CardContent className="flex flex-col items-center gap-4 p-6 text-center sm:flex-row sm:text-left">
          {data.avatarUrl ? (
            <img src={data.avatarUrl} alt={data.name} className="h-24 w-24 rounded-full object-cover" />
          ) : (
            <span className="flex h-24 w-24 items-center justify-center rounded-full bg-primary text-3xl font-semibold text-primary-foreground">
              {data.name.charAt(0).toUpperCase()}
            </span>
          )}
          <div className="flex-1">
            <h1 className="text-2xl font-bold">{data.name}</h1>
            {data.city && (
              <p className="mt-1 flex items-center justify-center gap-1 text-muted-foreground sm:justify-start">
                <MapPin className="h-4 w-4" /> {data.city}
              </p>
            )}
            <div className="mt-3 flex flex-wrap justify-center gap-4 text-sm text-muted-foreground sm:justify-start">
              {data.phone && (
                <a href={`tel:${data.phone}`} className="flex items-center gap-1 hover:text-foreground">
                  <Phone className="h-4 w-4" /> {data.phone}
                </a>
              )}
              {data.mobile && (
                <a href={`tel:${data.mobile}`} className="flex items-center gap-1 hover:text-foreground">
                  <Smartphone className="h-4 w-4" /> {data.mobile}
                </a>
              )}
            </div>
          </div>
        </CardContent>
      </Card>

      <h2 className="mb-4 text-xl font-semibold">Listings by {data.name}</h2>
      {data.properties.length === 0 ? (
        <EmptyState title="No active listings" description="This agent has no properties listed right now." />
      ) : (
        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {data.properties.map((p) => (
            <PropertyCard key={p.id} property={p} />
          ))}
        </div>
      )}

      <div className="mt-8">
        <Link to="/agents" className="text-sm font-medium text-primary hover:underline">
          ← Back to all agents
        </Link>
      </div>
    </div>
  );
}
