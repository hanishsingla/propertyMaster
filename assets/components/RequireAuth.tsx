import { useEffect } from 'react';
import { useNavigate } from '@tanstack/react-router';
import { useAuth } from '@/features/auth/AuthProvider';
import { FullSpinner } from '@/components/ui/spinner';
import { EmptyState } from '@/components/EmptyState';
import { ShieldAlert } from 'lucide-react';

export function RequireAuth({
  children,
  requireAgent = false,
}: {
  children: React.ReactNode;
  requireAgent?: boolean;
}) {
  const { isAuthenticated, isAgent, isLoading } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    if (!isLoading && !isAuthenticated) {
      navigate({ to: '/login', search: { redirect: window.location.pathname } });
    }
  }, [isLoading, isAuthenticated, navigate]);

  if (isLoading) return <FullSpinner />;
  if (!isAuthenticated) return <FullSpinner label="Redirecting…" />;

  if (requireAgent && !isAgent) {
    return (
      <div className="container py-12">
        <EmptyState
          icon={ShieldAlert}
          title="Agents only"
          description="This area is available to agent accounts. Upgrade your account to list properties."
        />
      </div>
    );
  }

  return <>{children}</>;
}
