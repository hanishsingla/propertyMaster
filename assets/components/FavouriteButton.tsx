import { useState } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { Heart } from 'lucide-react';
import { useNavigate } from '@tanstack/react-router';
import { apiClient } from '@/lib/apiClient';
import { cn } from '@/lib/utils';
import { useAuth } from '@/features/auth/AuthProvider';
import { useToast } from '@/components/ui/toast';

interface Props {
  propertyId: number;
  favourited: boolean;
  className?: string;
  variant?: 'overlay' | 'inline';
}

export function FavouriteButton({ propertyId, favourited, className, variant = 'overlay' }: Props) {
  const { isAuthenticated } = useAuth();
  const [active, setActive] = useState(favourited);
  const [busy, setBusy] = useState(false);
  const queryClient = useQueryClient();
  const navigate = useNavigate();
  const toast = useToast();

  const toggle = async (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    if (!isAuthenticated) {
      navigate({ to: '/login', search: { redirect: window.location.pathname } });
      return;
    }
    if (busy) return;
    setBusy(true);
    const next = !active;
    setActive(next);
    try {
      if (next) {
        await apiClient.post(`/api/favourites/${propertyId}`);
      } else {
        await apiClient.delete(`/api/favourites/${propertyId}`);
      }
      queryClient.invalidateQueries({ queryKey: ['favourites'] });
      queryClient.invalidateQueries({ queryKey: ['properties'] });
    } catch {
      setActive(!next);
      toast.error('Could not update favourite');
    } finally {
      setBusy(false);
    }
  };

  if (variant === 'inline') {
    return (
      <button
        type="button"
        onClick={toggle}
        disabled={busy}
        className={cn(
          'inline-flex items-center gap-2 rounded-md border border-input px-4 py-2 text-sm font-medium transition-colors hover:bg-secondary',
          active && 'border-destructive/40 text-destructive',
          className,
        )}
        aria-pressed={active}
      >
        <Heart className={cn('h-4 w-4', active && 'fill-destructive text-destructive')} />
        {active ? 'Saved' : 'Save'}
      </button>
    );
  }

  return (
    <button
      type="button"
      onClick={toggle}
      disabled={busy}
      aria-label={active ? 'Remove from favourites' : 'Add to favourites'}
      aria-pressed={active}
      className={cn(
        'flex h-9 w-9 items-center justify-center rounded-full bg-white/90 shadow-sm backdrop-blur transition hover:bg-white',
        className,
      )}
    >
      <Heart className={cn('h-5 w-5 text-foreground', active && 'fill-destructive text-destructive')} />
    </button>
  );
}
