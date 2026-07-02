import { Link } from '@tanstack/react-router';
import { Phone, Smartphone, MapPin } from 'lucide-react';
import type { Agent } from '@/lib/types';
import { Card } from '@/components/ui/card';

export function AgentCard({ agent }: { agent: Agent }) {
  return (
    <Link to="/agents/$id" params={{ id: String(agent.id) }} className="block focus-visible:outline-none">
      <Card className="flex flex-col items-center p-6 text-center transition-shadow hover:shadow-md">
        {agent.avatarUrl ? (
          <img src={agent.avatarUrl} alt={agent.name} className="h-20 w-20 rounded-full object-cover" />
        ) : (
          <span className="flex h-20 w-20 items-center justify-center rounded-full bg-primary text-2xl font-semibold text-primary-foreground">
            {agent.name.charAt(0).toUpperCase()}
          </span>
        )}
        <h3 className="mt-3 font-semibold">{agent.name}</h3>
        {agent.city && (
          <p className="mt-1 flex items-center gap-1 text-sm text-muted-foreground">
            <MapPin className="h-3.5 w-3.5" /> {agent.city}
          </p>
        )}
        <div className="mt-3 space-y-1 text-sm text-muted-foreground">
          {agent.phone && (
            <p className="flex items-center justify-center gap-1">
              <Phone className="h-3.5 w-3.5" /> {agent.phone}
            </p>
          )}
          {agent.mobile && (
            <p className="flex items-center justify-center gap-1">
              <Smartphone className="h-3.5 w-3.5" /> {agent.mobile}
            </p>
          )}
        </div>
      </Card>
    </Link>
  );
}
