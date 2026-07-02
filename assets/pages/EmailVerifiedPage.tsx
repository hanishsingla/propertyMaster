import { useSearch, Link } from '@tanstack/react-router';
import { CheckCircle2, XCircle } from 'lucide-react';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

export function EmailVerifiedPage() {
  const search = useSearch({ strict: false }) as { status?: string };
  const ok = search.status === 'ok';

  return (
    <div className="container flex justify-center py-16">
      <Card className="w-full max-w-md text-center">
        <CardContent className="flex flex-col items-center gap-4 p-8">
          {ok ? (
            <>
              <CheckCircle2 className="h-16 w-16 text-accent" />
              <h1 className="text-2xl font-bold">Email verified!</h1>
              <p className="text-muted-foreground">
                Your email address has been confirmed. You now have full access to your account.
              </p>
            </>
          ) : (
            <>
              <XCircle className="h-16 w-16 text-destructive" />
              <h1 className="text-2xl font-bold">Verification failed</h1>
              <p className="text-muted-foreground">
                This verification link is invalid or has expired. Try requesting a new one from your account.
              </p>
            </>
          )}
          <Link to="/">
            <Button>Go to homepage</Button>
          </Link>
        </CardContent>
      </Card>
    </div>
  );
}
