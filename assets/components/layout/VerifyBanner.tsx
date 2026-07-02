import { useState } from 'react';
import { AlertTriangle, X } from 'lucide-react';
import { apiClient } from '@/lib/apiClient';
import { useAuth } from '@/features/auth/AuthProvider';
import { useToast } from '@/components/ui/toast';
import { Button } from '@/components/ui/button';

export function VerifyBanner() {
  const { user } = useAuth();
  const toast = useToast();
  const [dismissed, setDismissed] = useState(false);
  const [sending, setSending] = useState(false);

  if (!user || user.isVerified || dismissed) return null;

  const resend = async () => {
    setSending(true);
    try {
      await apiClient.post('/api/auth/resend-verification');
      toast.success('Verification email sent. Check your inbox.');
    } catch {
      toast.error('Could not send verification email.');
    } finally {
      setSending(false);
    }
  };

  return (
    <div className="bg-amber-50 text-amber-900">
      <div className="container flex flex-wrap items-center gap-3 py-2.5 text-sm">
        <AlertTriangle className="h-4 w-4 flex-none" />
        <span className="flex-1">Please verify your email address to unlock all features.</span>
        <Button size="sm" variant="outline" onClick={resend} loading={sending}>
          Resend email
        </Button>
        <button onClick={() => setDismissed(true)} aria-label="Dismiss" className="rounded p-1 hover:bg-amber-100">
          <X className="h-4 w-4" />
        </button>
      </div>
    </div>
  );
}
