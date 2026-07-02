import { useState } from 'react';
import { useQuery, keepPreviousData } from '@tanstack/react-query';
import { Users } from 'lucide-react';
import { apiClient, buildQuery } from '@/lib/apiClient';
import type { Paginated, Agent } from '@/lib/types';
import { AgentCard } from '@/components/AgentCard';
import { Pagination } from '@/components/Pagination';
import { EmptyState } from '@/components/EmptyState';
import { Skeleton } from '@/components/Skeletons';

export function AgentsPage() {
  const [page, setPage] = useState(1);
  const { data, isLoading } = useQuery({
    queryKey: ['agents', { page }],
    queryFn: ({ signal }) =>
      apiClient.get<Paginated<Agent>>(`/api/agents${buildQuery({ page, perPage: 12 })}`, signal),
    placeholderData: keepPreviousData,
  });

  return (
    <div className="container py-8">
      <div className="mb-6">
        <h1 className="text-3xl font-bold">Our Agents</h1>
        <p className="mt-1 text-muted-foreground">Connect with trusted real-estate professionals.</p>
      </div>

      {isLoading ? (
        <div className="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
          {Array.from({ length: 8 }).map((_, i) => (
            <Skeleton key={i} className="h-56 w-full" />
          ))}
        </div>
      ) : !data || data.data.length === 0 ? (
        <EmptyState icon={Users} title="No agents yet" />
      ) : (
        <>
          <div className="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
            {data.data.map((a) => (
              <AgentCard key={a.id} agent={a} />
            ))}
          </div>
          <div className="mt-8">
            <Pagination meta={data.meta} onPageChange={setPage} />
          </div>
        </>
      )}
    </div>
  );
}
